<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function wishlist()
    {
        $products = Wishlist::query()
            ->join('shop_products', 'shop_products.id', '=', 'wishlists.product_id')
            ->join('products', 'products.id', '=', 'shop_products.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'shop_products.brand_id')
            ->where('wishlists.user_id', Auth::id())
            ->orderByDesc('wishlists.created_at')
            ->select(
                'wishlists.id as wishlist_id',
                'shop_products.id as shop_product_id',
                'products.id as product_id',
                'products.name',
                'products.eng_name',
                'products.image_url',
                'products.rating_avg',
                'products.rating_count',
                'shop_products.price',
                'brands.name as brand_name',
                'wishlists.created_at'
            )
            ->get();

        return view('website.wishlist', compact('products'));
    }

    public function favoriteUpdate($id)
    {
        $shopProductId = (int) $id;
        $userId = Auth::id();

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $shopProductId)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json([
                'action' => 'remove',
                'in_wishlist' => false,
                'wishlist_count' => Wishlist::where('user_id', $userId)->count(),
                'message' => 'Removed from wishlist',
            ]);
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $shopProductId,
        ]);

        return response()->json([
            'action' => 'add',
            'in_wishlist' => true,
            'wishlist_count' => Wishlist::where('user_id', $userId)->count(),
            'message' => 'Added to wishlist',
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        Wishlist::where('user_id', Auth::id())
            ->where('product_id', (int) $request->id)
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'action' => 'remove',
                'wishlist_count' => Wishlist::where('user_id', Auth::id())->count(),
                'message' => 'Removed from wishlist',
            ]);
        }

        return redirect()->route('wishlist')->with('success', 'Product removed from wishlist.');
    }
}
