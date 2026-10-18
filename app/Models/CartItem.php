<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'shop_product_id', 'quantity', 'price', 'source'];

    // Relationship: A cart item belongs to a cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // Relationship: A cart item belongs to a shop product
    public function shopProduct()
    {
        return $this->belongsTo(Shop_product::class, 'shop_product_id');
    }
}
