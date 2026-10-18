<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Setting;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Cache;

class CartService
{
    public static function totalQuantity(array $cart): int
    {
        return (int) array_sum(array_map(
            fn ($item) => (int) ($item['quantity'] ?? 0),
            $cart
        ));
    }

    public static function subtotal(array $cart): float
    {
        return round(array_sum(array_map(
            fn ($item) => ((float) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 0)),
            $cart
        )), 2);
    }

    /** @return array{subtotal: float, shipping: float, total: float, free_threshold: float, free: bool} */
    public static function totals(array $cart): array
    {
        $settings = Cache::remember(AppServiceProvider::SETTINGS_CACHE_KEY, 3600, function () {
            return Setting::pluck('value', 'key')->all();
        });
        $subtotal = self::subtotal($cart);
        $threshold = (float) ($settings['free_shipping_threshold'] ?? 50);
        $flat = (float) ($settings['flat_shipping'] ?? 3);
        $free = $subtotal >= $threshold;
        $shipping = ($cart && !$free) ? $flat : 0.0;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => round($subtotal + $shipping, 2),
            'free_threshold' => $threshold,
            'free' => $free,
        ];
    }

    public static function productIds(array $cart): array
    {
        return array_values(array_unique(array_map(
            fn ($item) => (int) ($item['id'] ?? 0),
            $cart
        )));
    }

    public static function merge(array $primary, array $secondary): array
    {
        $merged = $primary;

        foreach ($secondary as $shopProductId => $item) {
            if (isset($merged[$shopProductId])) {
                $merged[$shopProductId]['quantity'] += (int) ($item['quantity'] ?? 0);
            } else {
                $merged[$shopProductId] = $item;
            }
        }

        return $merged;
    }

    public static function loadFromDatabase(int $userId): array
    {
        $dbCart = Cart::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('items')
            ->first();

        if (!$dbCart) {
            return [];
        }

        $sessionCart = [];

        foreach ($dbCart->items as $item) {
            $product = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
                ->where('shop_products.id', '=', $item->shop_product_id)
                ->first(['products.id', 'products.eng_name', 'products.name', 'products.image_url']);

            if (!$product) {
                continue;
            }

            $sessionCart[$item->shop_product_id] = [
                'id' => $product->id,
                'shop_product_id' => $item->shop_product_id,
                'eng_name' => $product->eng_name,
                'name' => $product->name,
                'quantity' => (int) $item->quantity,
                'price' => $item->price,
                'image' => Product::imageUrl($product->image_url),
                'source' => $item->source,
            ];
        }

        return $sessionCart;
    }

    public static function persistToDatabase(int $userId, array $cart): void
    {
        if (empty($cart)) {
            self::clearPendingCart($userId);
            return;
        }

        $dbCart = Cart::firstOrCreate(
            ['user_id' => $userId, 'status' => 'pending'],
            ['total' => 0]
        );

        $dbCart->items()->delete();

        $total = 0;

        foreach ($cart as $shopProductId => $item) {
            CartItem::create([
                'cart_id' => $dbCart->id,
                'shop_product_id' => $shopProductId,
                'quantity' => (int) ($item['quantity'] ?? 1),
                'price' => $item['price'],
                'source' => $item['source'] ?? null,
            ]);

            $total += $item['price'] * (int) ($item['quantity'] ?? 1);
        }

        $dbCart->update(['total' => $total]);
    }

    public static function clearPendingCart(int $userId): void
    {
        Cart::where('user_id', $userId)
            ->where('status', 'pending')
            ->each(function (Cart $cart) {
                $cart->items()->delete();
                $cart->delete();
            });
    }

    public static function saveSessionCart(array $cart): array
    {
        session()->put('cart', $cart);

        if (auth()->check()) {
            session(['cart_owner_id' => auth()->id()]);
            self::persistToDatabase(auth()->id(), $cart);
        }

        return $cart;
    }

    public static function clearSessionCart(): void
    {
        session()->forget(['cart', 'cart_owner_id']);
    }

    public static function restoreForUser(int $userId, array $guestCart = []): array
    {
        $dbCart = self::loadFromDatabase($userId);
        $merged = self::merge($guestCart, $dbCart);

        session(['cart' => $merged, 'cart_owner_id' => $userId]);
        self::persistToDatabase($userId, $merged);

        return $merged;
    }

    public static function clearForLogout(int $userId): void
    {
        self::clearPendingCart($userId);
        self::clearSessionCart();
    }
}
