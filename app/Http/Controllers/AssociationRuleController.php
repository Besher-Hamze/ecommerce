<?php

namespace App\Http\Controllers;

use App\Models\AssociationRule;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\AprioriService;
use Illuminate\Http\Request;

class AssociationRuleController extends Controller
{
    /**
     * Admin page: run Apriori and display discovered rules
     */
    public function index(Request $request)
    {
        $minSupport = (float) $request->input('min_support', 0.03);
        $minConfidence = (float) $request->input('min_confidence', 0.35);
        $maxItemsetSize = (int) $request->input('max_itemset_size', 3);

        $transactions = $this->loadTransactions();

        $rules = [];
        if ($request->has('run')) {
            $service = new AprioriService($transactions);
            $rules = $service->generateRules($minSupport, $minConfidence, $maxItemsetSize);

            // Save latest run (replace old cache)
            AssociationRule::query()->delete();
            foreach ($rules as $rule) {
                AssociationRule::create(array_merge($rule, [
                    'min_support' => $minSupport,
                    'min_confidence' => $minConfidence,
                ]));
            }
        } else {
            $rules = AssociationRule::query()
                ->orderByDesc('lift')
                ->orderByDesc('confidence')
                ->limit(200)
                ->get()
                ->toArray();
        }

        $summary = $this->buildSummary($transactions, $rules);

        return view('dashboard.association-rules.index', compact(
            'rules',
            'summary',
            'minSupport',
            'minConfidence',
            'maxItemsetSize'
        ));
    }

    /**
     * Front-end recommendations based on cart/product context
     */
    public function recommendations(Request $request)
    {
        $productId = (int) $request->input('product_id');
        $product = Product::find($productId);

        $recommendations = collect();

        if ($product) {
            $rules = AssociationRule::query()
                ->positiveCorrelation()
                ->orderByDesc('lift')
                ->get();

            foreach ($rules as $rule) {
                $antecedent = $rule->antecedent ?? [];
                $consequent = $rule->consequent ?? [];

                if (in_array($product->name, $antecedent, true)) {
                    foreach ($consequent as $recommendedName) {
                        $recommendedProduct = Product::where('name', $recommendedName)->first();
                        if ($recommendedProduct) {
                            $recommendations->push([
                                'product' => $recommendedProduct,
                                'rule' => $rule,
                            ]);
                        }
                    }
                }
            }

            // unique by product id and keep strongest rule
            $recommendations = $recommendations
                ->sortByDesc(fn($r) => $r['rule']->lift * $r['rule']->confidence)
                ->unique(fn($r) => $r['product']->id)
                ->take(8)
                ->values();
        }

        return view('website.recommendations', compact('product', 'recommendations'));
    }

    /**
     * Convert orders into transaction baskets
     */
    protected function loadTransactions(): array
    {
        // 1) Purchase behavior from completed baskets (orders)
        $orders = Order::with('products')->get();

        $transactions = [];
        foreach ($orders as $order) {
            $items = $order->products->pluck('name')->filter()->values()->toArray();
            if (count($items) > 0) {
                $transactions[] = $items;
            }
        }

        // 2) Intent behavior from wishlist baskets (grouped by user)
        $wishlistByUser = Wishlist::with('product.product')
            ->get()
            ->groupBy('user_id');

        foreach ($wishlistByUser as $userWishlists) {
            $items = [];
            foreach ($userWishlists as $wishlist) {
                $name = optional(optional($wishlist->product)->product)->name;
                if (!empty($name)) {
                    $items[] = $name;
                }
            }

            $items = array_values(array_unique($items));
            if (count($items) > 1) {
                $transactions[] = $items;
            }
        }

        return $transactions;
    }

    /**
     * Build dataset and rule analysis summary
     */
    protected function buildSummary(array $transactions, array $rules): array
    {
        $allItems = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction as $item) {
                $allItems[] = $item;
            }
        }

        $itemFrequency = array_count_values($allItems);
        arsort($itemFrequency);

        $topRules = collect($rules)
            ->sortByDesc(fn($r) => ($r['lift'] ?? 0) * ($r['confidence'] ?? 0))
            ->take(5)
            ->values()
            ->all();

        return [
            'transactions_count' => count($transactions),
            'unique_products_count' => count(array_unique($allItems)),
            'avg_basket_size' => count($transactions) > 0
                ? round(count($allItems) / count($transactions), 2)
                : 0,
            'top_products' => array_slice($itemFrequency, 0, 10, true),
            'rules_count' => count($rules),
            'top_rules' => $topRules,
        ];
    }
}
