<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Shop_product;
use App\Models\Product;
use Phpml\Association\Apriori;
use Illuminate\Http\Request;

class AprioriController extends Controller
{
    /**
     * Generate Association Rules using Apriori Algorithm
     * Route: GET /admin/apriori/rules
     */
    public function generateRules(Request $request)
    {
        // Get support and confidence from request (with defaults)
        $support = $request->input('support', 0.2); // 20% default
        $confidence = $request->input('confidence', 0.5); // 50% default

        // 1. Prepare transactions dataset
        // Extract shop_product_ids grouped by their cart_id
        $transactions = CartItem::select('cart_id', 'shop_product_id')
            ->get()
            ->groupBy('cart_id')
            ->map(function ($items) {
                // PHP-ML requires string values for items
                return $items->pluck('shop_product_id')
                    ->map(fn($id) => (string)$id)
                    ->toArray();
            })
            ->values()
            ->toArray();

        // Check if we have enough transaction data
        if (empty($transactions) || count($transactions) < 3) {
            return response()->json([
                'message' => 'Not enough cart data to generate rules. Need at least 3 completed carts.',
                'total_transactions' => count($transactions)
            ], 422);
        }

        try {
            // 2. Initialize and train Apriori model
            $apriori = new Apriori($support, $confidence);
            $apriori->train($transactions, []);

            // 3. Retrieve Association Rules
            $rules = $apriori->getRules();

            // 4. Format rules with product names
            $formattedRules = [];
            foreach ($rules as $rule) {
                $antecedent = $this->getProductDetails($rule['antecedent']);
                $consequent = $this->getProductDetails($rule['consequent']);

                $formattedRules[] = [
                    'If_buys' => $antecedent,
                    'Then_buys' => $consequent,
                    'Confidence' => round($rule['confidence'] * 100, 2) . '%',
                    'Support' => round($rule['support'] * 100, 2) . '%',
                    'Lift' => isset($rule['lift']) ? round($rule['lift'], 2) : 'N/A'
                ];
            }

            return response()->json([
                'success' => true,
                'total_transactions' => count($transactions),
                'support_threshold' => $support * 100 . '%',
                'confidence_threshold' => $confidence * 100 . '%',
                'total_rules' => count($formattedRules),
                'rules' => $formattedRules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating rules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product details from shop_product IDs
     */
    private function getProductDetails(array $shopProductIds)
    {
        if (empty($shopProductIds)) {
            return [];
        }

        return Shop_product::whereIn('shop_products.id', $shopProductIds)
            ->join('products', 'products.id', '=', 'shop_products.product_id')
            ->select(
                'shop_products.id',
                'products.eng_name',
                'products.name',
                'shop_products.price'
            )
            ->get()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->eng_name,
                'arabic_name' => $product->name,
                'price' => '$' . number_format($product->price, 2)
            ])
            ->toArray();
    }

    /**
     * Display Apriori analysis page with form
     * Route: GET /admin/apriori
     */
    public function index()
    {
        $totalCarts = \App\Models\Cart::where('status', 'completed')->count();
        $totalItems = CartItem::count();

        return view('dashboard.apriori.index', compact('totalCarts', 'totalItems'));
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'total_carts' => \App\Models\Cart::count(),
            'completed_carts' => \App\Models\Cart::where('status', 'completed')->count(),
            'pending_carts' => \App\Models\Cart::where('status', 'pending')->count(),
            'total_cart_items' => CartItem::count(),
            'average_items_per_cart' => CartItem::count() / max(\App\Models\Cart::count(), 1),
            'unique_products' => Shop_product::whereIn('id', CartItem::select('shop_product_id')->distinct())->count(),
        ];

        return response()->json($stats);
    }
}
