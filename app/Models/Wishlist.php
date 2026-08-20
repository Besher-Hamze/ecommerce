<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Wishlist extends Model
{
    use HasFactory;
    protected $table = "wishlists";
    protected $fillable = [
        'product_id',
        'user_id',
    ];
	  protected $casts = [
        'user_id' => 'integer',
		 'product_id' => 'integer',
		 
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Shop_product::class);
    }
    public static function countWishlist($product_id)
    {
        $countWishlist = Wishlist::where([
            'user_id' => Auth::user()->id,
            'product_id' => $product_id
        ])->count();
        return $countWishlist;
    }
	public static function countWishlistuser($user_id)
	{ $countWishlist = Wishlist::where([
            'user_id' => Auth::user()->id,
        ])->count();
        return $countWishlist;
	}
}
