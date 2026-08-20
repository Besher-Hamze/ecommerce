<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;
class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
	 protected $casts = [
          'quantity' => 'integer',
		  'id'=>'integer'
    ];
	public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')->withPivot('quantity');
    }
     public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
