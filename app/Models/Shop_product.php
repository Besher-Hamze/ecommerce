<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop_product extends Model
{
    use HasFactory;
	  protected $casts = [
        'price' => 'double',
    ];
          protected $fillable=['product_id','price','quantity','product_colors','product_type','category_id','brand_id'];

	  public function categry()
    {
        return $this->belongsTo('App\Models\Category','category_id');
    }
    public function wishlist(){
        return $this->hasMany(Wishlist::class);
     }
     public function images()
    {
        return $this->hasMany(Image::class,'product_id');
    }
    public function product()
    {
	 return $this->hasOne(Product::class,'product_id');
    }
    
public function reviews()
{
    return $this->hasMany(Review::class,'product_id');
}
}
