<?php

namespace App\Services\Recommendation;

use Illuminate\Support\Facades\DB;

/**
 * Loads the behavioural data the models are trained on.
 */
class RecommendationData
{
    /**
     * Completed carts as transactions.
     *
     * @return array<int, array{user:int|null, date:string, items:int[]}> cart id => basket
     */
    public function baskets(): array
    {
        $rows = DB::table('cart_items')
            ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
            ->join('shop_products', 'shop_products.id', '=', 'cart_items.shop_product_id')
            ->where('carts.status', 'completed')
            ->orderBy('carts.id')
            ->get(['carts.id as cart_id', 'carts.user_id', 'carts.created_at', 'shop_products.product_id']);

        $baskets = [];
        foreach ($rows as $row) {
            $baskets[$row->cart_id] ??= ['user' => $row->user_id, 'date' => (string) $row->created_at, 'items' => []];
            $baskets[$row->cart_id]['items'][$row->product_id] = (int) $row->product_id;
        }
        foreach ($baskets as &$basket) {
            $basket['items'] = array_values($basket['items']);
        }

        return $baskets;
    }

    /**
     * @return array<int, array> product id => features
     */
    public function products(): array
    {
        $rows = DB::table('products')
            ->join('shop_products', 'shop_products.product_id', '=', 'products.id')
            ->leftJoin('brands', 'brands.id', '=', 'shop_products.brand_id')
            ->get([
                'products.id', 'products.eng_name', 'products.eng_description', 'products.form', 'products.tags',
                'products.rating_avg', 'products.rating_count', 'products.sales_count',
                'shop_products.price', 'shop_products.product_type', 'shop_products.brand_id', 'brands.name as brand',
            ]);

        $products = [];
        foreach ($rows as $r) {
            $products[(int) $r->id] = [
                'name' => (string) $r->eng_name,
                'description' => (string) $r->eng_description,
                'brand' => (string) $r->brand,
                'brand_id' => (int) $r->brand_id,
                'type' => (string) $r->product_type,
                'form' => $r->form,
                'tags' => json_decode($r->tags ?? '[]', true) ?: [],
                'price' => (float) $r->price,
                'rating_avg' => (float) $r->rating_avg,
                'rating_count' => (int) $r->rating_count,
                'sales' => (int) $r->sales_count,
            ];
        }

        return $products;
    }

    /**
     * Implicit feedback events.
     *
     * @return array<int, array{user:int, item:int, kind:string, date:string}>
     */
    public function feedback(): array
    {
        $events = [];
        foreach (DB::table('product_views')->whereNotNull('user_id')->get(['user_id', 'product_id', 'created_at']) as $v) {
            $events[] = ['user' => (int) $v->user_id, 'item' => (int) $v->product_id, 'kind' => 'view', 'date' => (string) $v->created_at];
        }
        $wishlists = DB::table('wishlists')
            ->join('shop_products', 'shop_products.id', '=', 'wishlists.product_id')
            ->get(['wishlists.user_id', 'shop_products.product_id', 'wishlists.created_at']);
        foreach ($wishlists as $w) {
            $events[] = ['user' => (int) $w->user_id, 'item' => (int) $w->product_id, 'kind' => 'wishlist', 'date' => (string) $w->created_at];
        }

        return $events;
    }

    /**
     * Builds the implicit user-item matrix from baskets + feedback events.
     *
     * @return array<int, array<int, float>>
     */
    public static function userItemMatrix(array $baskets, array $feedback): array
    {
        $matrix = [];
        foreach ($feedback as $e) {
            $w = ItemBasedCF::WEIGHTS[$e['kind']];
            $matrix[$e['user']][$e['item']] = max($matrix[$e['user']][$e['item']] ?? 0, $w);
        }
        foreach ($baskets as $basket) {
            if (!$basket['user']) {
                continue;
            }
            foreach ($basket['items'] as $item) {
                $matrix[$basket['user']][$item] = ItemBasedCF::WEIGHTS['purchase'];
            }
        }

        return $matrix;
    }
}
