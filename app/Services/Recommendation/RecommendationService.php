<?php

namespace App\Services\Recommendation;

use App\Models\Product;
use App\Models\ProductRecommendation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Online serving ("speed layer"): reads the pre-computed models and returns
 * ready-to-render product collections for the storefront widgets.
 * Every returned product carries `rec_meta` (why it was recommended).
 */
class RecommendationService
{
    private const TTL = 1800;

    public function crossSell(int $productId, int $k = 8): Collection
    {
        return $this->stored(ProductRecommendation::CROSS_SELL, $productId, $k);
    }

    public function upsell(int $productId, int $k = 3): Collection
    {
        return $this->stored(ProductRecommendation::UPSELL, $productId, $k);
    }

    public function similar(int $productId, int $k = 8): Collection
    {
        return $this->stored(ProductRecommendation::SIMILAR, $productId, $k);
    }

    public function alsoLiked(int $productId, int $k = 8): Collection
    {
        return $this->stored(ProductRecommendation::CF, $productId, $k);
    }

    /**
     * Cart-level cross-selling: rules whose whole antecedent is contained in the cart
     * (jsonb containment, GIN-indexed), merged with the per-item lists.
     *
     * @param int[] $productIds
     */
    public function forCart(array $productIds, int $k = 8): Collection
    {
        $productIds = array_values(array_unique(array_map('intval', $productIds)));
        if (!$productIds) {
            return collect();
        }
        $inCart = array_flip($productIds);
        $scores = [];

        $rules = DB::select(
            "SELECT (consequent->>0)::int AS id, MAX(confidence) AS confidence, MAX(lift) AS lift,
                    MAX(0.6 * confidence + 0.4 * GREATEST(0, 1 - 1 / lift)) AS score,
                    MAX(antecedent_size) AS antecedent_size
             FROM association_rules
             WHERE level = 'product' AND antecedent <@ ?::jsonb AND jsonb_array_length(consequent) = 1
             GROUP BY 1 ORDER BY score DESC LIMIT 40",
            [json_encode($productIds)]
        );
        foreach ($rules as $r) {
            if (!isset($inCart[$r->id])) {
                // rules learned from several cart items at once are more specific
                $scores[$r->id] = ['score' => $r->score + 0.1 * ($r->antecedent_size - 1), 'level' => 'product',
                    'confidence' => round($r->confidence, 4), 'lift' => round($r->lift, 2)];
            }
        }

        $stored = ProductRecommendation::where('type', ProductRecommendation::CROSS_SELL)
            ->whereIn('product_id', $productIds)
            ->orderBy('rank')
            ->get(['recommended_product_id', 'score', 'meta']);
        foreach ($stored as $row) {
            $id = $row->recommended_product_id;
            if (!isset($inCart[$id]) && (!isset($scores[$id]) || $scores[$id]['score'] < $row->score)) {
                $scores[$id] = ['score' => $row->score] + ($row->meta ?? []);
            }
        }

        uasort($scores, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $this->hydrate(array_slice($scores, 0, $k, true));
    }

    /**
     * Best upgrade for each cart line.
     *
     * @param int[] $productIds
     * @return Collection product id in cart => upgrade product
     */
    public function upsellsForCart(array $productIds): Collection
    {
        if (!$productIds) {
            return collect();
        }
        $rows = ProductRecommendation::where('type', ProductRecommendation::UPSELL)
            ->whereIn('product_id', $productIds)
            ->whereNotIn('recommended_product_id', $productIds)
            ->orderBy('rank')
            ->get(['product_id', 'recommended_product_id', 'score', 'meta'])
            ->unique('product_id');

        $products = $this->hydrate($rows->mapWithKeys(fn ($r) => [$r->recommended_product_id => ($r->meta ?? []) + ['score' => $r->score]])->all())
            ->keyBy('id');

        return $rows->mapWithKeys(fn ($r) => [$r->product_id => $products->get($r->recommended_product_id)])->filter();
    }

    /**
     * Personalised top-N with item-based CF. History weights decay with age
     * (half-life 30 days): purchases/cart 3, wishlist 2, views 1.
     */
    public function personalized(?int $userId, string $sessionId, array $cartProductIds = [], int $k = 12): Collection
    {
        $history = [];
        $exclude = array_flip($cartProductIds);
        $add = function ($productId, $weight, $date) use (&$history) {
            $age = max(0, (time() - strtotime((string) $date)) / 86400);
            $history[$productId] = ($history[$productId] ?? 0) + $weight * pow(0.5, $age / 30);
        };

        foreach ($cartProductIds as $id) {
            $history[$id] = ($history[$id] ?? 0) + ItemBasedCF::WEIGHTS['purchase'];
        }
        if ($userId) {
            $purchases = DB::table('order_items')->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.user_id', $userId)->where('orders.created_at', '>=', now()->subDays(365))
                ->get(['order_items.product_id', 'orders.created_at']);
            foreach ($purchases as $p) {
                $add($p->product_id, ItemBasedCF::WEIGHTS['purchase'], $p->created_at);
                $exclude[$p->product_id] = true;
            }
            $wishlist = DB::table('wishlists')->join('shop_products', 'shop_products.id', '=', 'wishlists.product_id')
                ->where('wishlists.user_id', $userId)->get(['shop_products.product_id', 'wishlists.created_at']);
            foreach ($wishlist as $w) {
                $add($w->product_id, ItemBasedCF::WEIGHTS['wishlist'], $w->created_at);
            }
        }
        $views = DB::table('product_views')
            ->where(fn ($q) => $userId ? $q->where('user_id', $userId)->orWhere('session_id', $sessionId) : $q->where('session_id', $sessionId))
            ->where('created_at', '>=', now()->subDays(90))
            ->orderByDesc('created_at')->limit(60)
            ->get(['product_id', 'created_at']);
        foreach ($views as $v) {
            $add($v->product_id, ItemBasedCF::WEIGHTS['view'], $v->created_at);
        }

        if (!$history) {
            return collect();
        }
        arsort($history);
        $history = array_slice($history, 0, 40, true);

        $values = implode(',', array_fill(0, count($history), '(?::bigint, ?::float)'));
        $bindings = [];
        foreach ($history as $id => $w) {
            array_push($bindings, $id, $w);
        }
        $rows = DB::select(
            "SELECT pr.recommended_product_id AS id, SUM(pr.score * h.w) AS score, COUNT(*) AS support
             FROM product_recommendations pr JOIN (VALUES {$values}) AS h(pid, w) ON h.pid = pr.product_id
             WHERE pr.type = 'cf' GROUP BY pr.recommended_product_id ORDER BY score DESC LIMIT 60",
            $bindings
        );

        $scores = [];
        foreach ($rows as $r) {
            if (!isset($exclude[$r->id]) && count($scores) < $k) {
                $scores[$r->id] = ['score' => round($r->score, 4), 'level' => 'cf', 'support' => (int) $r->support];
            }
        }

        return $this->hydrate($scores);
    }

    /** Best sellers of the last 30 days (cold-start fallback). */
    public function trending(int $k = 12, array $exclude = []): Collection
    {
        $ids = Cache::remember('reco.trending', 900, function () {
            $recent = DB::table('order_items')->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.created_at', '>=', now()->subDays(30))->where('orders.state', '<>', 2)
                ->groupBy('order_items.product_id')->orderByRaw('SUM(order_items.quantity) DESC')
                ->limit(40)->pluck('order_items.product_id')->all();

            return $recent ?: DB::table('products')->orderByDesc('sales_count')->limit(40)->pluck('id')->all();
        });
        $ids = array_slice(array_values(array_diff($ids, $exclude)), 0, $k);

        return $this->hydrate(array_fill_keys($ids, ['level' => 'trending']));
    }

    public function recentlyViewed(?int $userId, string $sessionId, int $k = 8, ?int $exceptId = null): Collection
    {
        $rows = DB::table('product_views')
            ->where(fn ($q) => $userId ? $q->where('user_id', $userId)->orWhere('session_id', $sessionId) : $q->where('session_id', $sessionId))
            ->when($exceptId, fn ($q) => $q->where('product_id', '<>', $exceptId))
            ->groupBy('product_id')->orderByRaw('MAX(created_at) DESC')->limit($k)
            ->pluck('product_id')->all();

        return $this->hydrate(array_fill_keys($rows, ['level' => 'history']));
    }

    private function stored(string $type, int $productId, int $k): Collection
    {
        $version = Cache::get(RecommendationTrainer::CACHE_VERSION_KEY, 0);

        $scores = Cache::remember("reco.v{$version}.{$type}.{$productId}.{$k}", self::TTL, function () use ($type, $productId, $k) {
            return ProductRecommendation::where('type', $type)->where('product_id', $productId)
                ->orderBy('rank')->limit($k)->get(['recommended_product_id', 'score', 'meta'])
                ->mapWithKeys(fn ($r) => [$r->recommended_product_id => ($r->meta ?? []) + ['score' => $r->score]])
                ->all();
        });

        return $this->hydrate($scores);
    }

    /**
     * @param array<int, array> $scores product id => meta, already ranked
     */
    private function hydrate(array $scores): Collection
    {
        if (!$scores) {
            return collect();
        }
        $products = Product::catalog()->whereIn('products.id', array_keys($scores))->get()->keyBy('id');

        return collect($scores)
            ->map(function ($meta, $id) use ($products) {
                $product = $products->get($id);
                if ($product) {
                    $product->rec_meta = $meta;
                }

                return $product;
            })
            ->filter()
            ->values();
    }
}
