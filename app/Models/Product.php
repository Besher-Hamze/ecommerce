<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;
use App\Models\Order;
class Product extends Model
{
    use HasFactory;
	protected $table="products";
	  protected $casts = [
        'count' => 'integer',
		 'product_code' => 'string',
		  'id'=>'integer'
    ];
	
      protected $fillable=['name','eng_name','description','eng_description','image_url'];

	public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_products', 'shop_id', 'product_id');
    }
	public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'order_id', 'product_id')->withPivot('quantity');
    }
    public function reviews()
{
    return $this->hasMany(Review::class, 'product_id');
}
    public function category()
{
    return $this->hasOneThrough(
        \App\Models\Category::class,
        \App\Models\Shop_product::class,
        'product_id',     // Foreign key on shop_products table...
        'id',             // Foreign key on categories table...
        'id',             // Local key on products table...
        'category_id'     // Local key on shop_products table...
    );
}



}
