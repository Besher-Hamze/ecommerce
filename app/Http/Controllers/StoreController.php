<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use App\Services\Recommendation\EventTracker;
use App\Services\Recommendation\RecommendationService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __construct(private RecommendationService $reco)
    {
    }

    public function cartCount()
    {
        return response()->json([
            'cart_count' => CartService::totalQuantity(session('cart', [])),
        ]);
    }

    public function cartList()
    {
        $cart = session('cart', []);
        $totals = CartService::totals($cart);
        $productIds = CartService::productIds($cart);

        $crossSell = $this->reco->forCart($productIds, 8);
        $upsells = $this->reco->upsellsForCart($productIds);

        EventTracker::impressions('cross_sell', $crossSell);
        EventTracker::impressions('upsell', $upsells->values());

        return view('website.cart', compact('cart', 'totals', 'crossSell', 'upsells'));
    }

    public function miniCart()
    {
        $cart = session('cart', []);
        $totals = CartService::totals($cart);

        return response()->json([
            'cart_count' => CartService::totalQuantity($cart),
            'html' => view('website.partials.mini-cart', compact('cart', 'totals'))->render(),
            'totals' => $totals,
        ]);
    }

    public function addToCart(Request $request, $id)
    {
        $qty = max(1, (int) $request->input('qty', 1));
        $source = $request->input('source');
        $context = $request->filled('context') ? (int) $request->input('context') : null;

        $product = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
            ->where('shop_products.id', '=', $id)
            ->first(['products.id', 'products.eng_name', 'products.name', 'products.image_url', 'shop_products.price']);

        if (!$product) {
            return response()->json(['status' => 'Product not found.'], 404);
        }

        $cart = session('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $qty;
            if ($source && empty($cart[$id]['source'])) {
                $cart[$id]['source'] = $source;
            }
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'shop_product_id' => (int) $id,
                'eng_name' => $product->eng_name,
                'name' => $product->name,
                'quantity' => $qty,
                'price' => (float) $product->price,
                'image' => Product::imageUrl($product->image_url),
                'source' => $source,
            ];
        }

        CartService::saveSessionCart($cart);

        if ($source) {
            EventTracker::record('add_to_cart', $source, (int) $product->id, $context, (float) $product->price);
        }

        return $this->cartJsonResponse($cart);
    }

    public function addToCartFromDetail(Request $request)
    {
        return $this->addToCart($request, $request->id);
    }

    /** Add a frequently-bought-together bundle in one click. */
    public function addBundle(Request $request)
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        $context = $request->filled('context') ? (int) $request->input('context') : null;
        $cart = session('cart', []);

        foreach ($ids as $shopProductId) {
            $product = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
                ->where('shop_products.id', $shopProductId)
                ->first(['products.id', 'products.eng_name', 'products.name', 'products.image_url', 'shop_products.price']);

            if (!$product) {
                continue;
            }

            if (isset($cart[$shopProductId])) {
                $cart[$shopProductId]['quantity'] += 1;
            } else {
                $cart[$shopProductId] = [
                    'id' => $product->id,
                    'shop_product_id' => $shopProductId,
                    'eng_name' => $product->eng_name,
                    'name' => $product->name,
                    'quantity' => 1,
                    'price' => (float) $product->price,
                    'image' => Product::imageUrl($product->image_url),
                    'source' => 'bundle',
                ];
            }

            EventTracker::record('add_to_cart', 'bundle', (int) $product->id, $context, (float) $product->price);
        }

        CartService::saveSessionCart($cart);

        return $this->cartJsonResponse($cart, 'Bundle added to cart.');
    }

    /** Replace a cart line with its recommended upgrade. */
    public function swapUpsell(Request $request)
    {
        $fromShopId = (int) $request->input('from');
        $toShopId = (int) $request->input('to');
        $cart = session('cart', []);

        if (!isset($cart[$fromShopId])) {
            return response()->json(['status' => 'Item not in cart.'], 404);
        }

        $upgrade = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
            ->where('shop_products.id', $toShopId)
            ->first(['products.id', 'products.eng_name', 'products.name', 'products.image_url', 'shop_products.price']);

        if (!$upgrade) {
            return response()->json(['status' => 'Upgrade not found.'], 404);
        }

        $qty = (int) $cart[$fromShopId]['quantity'];
        unset($cart[$fromShopId]);

        if (isset($cart[$toShopId])) {
            $cart[$toShopId]['quantity'] += $qty;
        } else {
            $cart[$toShopId] = [
                'id' => $upgrade->id,
                'shop_product_id' => $toShopId,
                'eng_name' => $upgrade->eng_name,
                'name' => $upgrade->name,
                'quantity' => $qty,
                'price' => (float) $upgrade->price,
                'image' => Product::imageUrl($upgrade->image_url),
                'source' => 'upsell',
            ];
        }

        CartService::saveSessionCart($cart);
        EventTracker::record('add_to_cart', 'upsell', (int) $upgrade->id, (int) ($request->input('context') ?: 0) ?: null, (float) $upgrade->price);

        if ($request->expectsJson()) {
            return $this->cartJsonResponse($cart, 'Upgraded successfully.');
        }

        return redirect()->route('cart.list')->with('success', 'Cart upgraded.');
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $quantity = max(1, (int) $request->input('quantity', 1));
        $cart = session('cart', []);

        if ($id && isset($cart[$id])) {
            $cart[$id]['quantity'] = $quantity;
            CartService::saveSessionCart($cart);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'Cart updated',
                'cart_count' => CartService::totalQuantity($cart),
                'totals' => CartService::totals($cart),
                'line_total' => isset($cart[$id]) ? round($cart[$id]['price'] * $cart[$id]['quantity'], 2) : 0,
            ]);
        }

        return redirect()->route('cart.list');
    }

    public function remove(Request $request)
    {
        $id = $request->input('id');
        $cart = session('cart', []);

        if ($id && isset($cart[$id])) {
            unset($cart[$id]);
            CartService::saveSessionCart($cart);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'Product removed',
                'cart_count' => CartService::totalQuantity($cart),
                'totals' => CartService::totals($cart),
            ]);
        }

        return redirect()->route('cart.list')->with('success', 'Product removed successfully');
    }

    private function cartJsonResponse(array $cart, string $message = 'Product added to cart successfully.')
    {
        return response()->json([
            'status' => $message,
            'cart_count' => CartService::totalQuantity($cart),
            'totals' => CartService::totals($cart),
        ]);
    }
}
