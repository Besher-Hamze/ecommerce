<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Shop_product;
use App\Models\Wishlist;
use Symfony\Component\HttpKernel\HttpCache\ResponseCacheStrategy;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function wishlist()
    {
        if (Auth::check()) {
            $products = Wishlist::join('shop_products', 'shop_products.id', '=', 'wishlists.product_id')->join('products', 'products.id', '=', 'shop_products.product_id')->where(
                'wishlists.user_id',
                '=',
                auth()->user()->id
            )->select('shop_products.id as shop_product_id', 'products.id as product_id', 'products.name', 'products.eng_name', 'shop_products.price', 'wishlists.created_at', 'products.image_url')->get();
            return view('wishlist', compact('products'));
        } else {
            return redirect()->route('login');
        }
    }

    public function favoriteUpdate($id)
    {
        $product = Shop_product::find($id);
        $user = auth()->user();
        $countWishlist = Wishlist::countwishlist($id);
        $wishlist = new Wishlist;
        if ($countWishlist == 0) {
            $wishlist->product_id = $id;
            $wishlist->user_id = $user->id;
            $wishlist->save();
            return response()->json(['action' => 'add', 'message' => 'Product Added Successfully to Wishlist']);
        } else {
            Wishlist::where(['user_id' => $user->id, 'product_id' => $id])->delete();
            return response()->json(['action' => 'remove', 'message' => 'Product Removed Successfully from Wishlist']);
        }
    }

    public function delete(Request $request)
    {
        $user_id = auth()->user()->id;
        Wishlist::where('user_id', '=', $user_id)->where('product_id', '=', $request->id)->delete();
        session()->flash('success', 'Product Removed Successfully from Wishlist !');
        return response()->json(['action' => 'remove', 'message' => 'Product Removed Successfully from Wishlist']);
    }
}
