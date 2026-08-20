<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function cartCount()
    {
        $cart = session()->get('cart', []);

        $count = 0;

        foreach ($cart as $item) {
            $count += (int) ($item['quantity'] ?? $item['qty'] ?? 0);
        }

        return response()->json([
            'cart_count' => $count
        ]);
    }
    public function cartList()
    {
        $cart = session()->get('cart', []);

        return view('website.cart')->with('cart', $cart);
    }

    public function addToCart(Request $request, $id)
    {
        $qty = max(1, (int) $request->input('qty', 1));

        $product = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
            ->where('shop_products.id', '=', $id)
            ->first();

        if (!$product) {
            return response()->json(['status' => 'Product not found.'], 404);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $qty;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'shop_product_id' => $id,
                'eng_name' => $product->eng_name,
                'name' => $product->name,
                'quantity' => $qty,
                'price' => $product->price,
                'image' => $product->image_url,
            ];
        }

        CartService::saveSessionCart($cart);

        return $this->cartJsonResponse($cart);
    }

    public function addToCartFromDetail(Request $request)
    {
        return $this->addToCart($request, $request->id);
    }

    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart', []);

            if (isset($cart[$request->id])) {
                $cart[$request->id]['quantity'] = max(1, (int) $request->quantity);
                CartService::saveSessionCart($cart);
                session()->flash('success', 'Cart updated successfully');
            }
        }
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart', []);

            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                CartService::saveSessionCart($cart);
            }

            session()->flash('success', 'Product removed successfully');
        }
    }

    private function cartJsonResponse(array $cart, string $message = 'Product added to cart successfully.')
    {
        return response()->json([
            'status' => $message,
            'cart_count' => CartService::totalQuantity($cart),
        ]);
    }
}
