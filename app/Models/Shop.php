<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
class Shop extends Model
{
    use HasFactory;
    protected $table="shops";
	 protected $fillable = [
        'name',
        'description',
        'eng_name',
        'eng_description',
		'city',
		'phone',
		'status'
    ];
	public function products()
    {
        return $this->belongsToMany(Product::class, 'shop_products', 'shop_id', 'product_id');
    }
}
