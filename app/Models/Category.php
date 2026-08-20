<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop_product;
use Illuminate\Support\Facades\Cache;
class Category extends Model
{
    use HasFactory;
    protected $table="categories";
	public $incrementing = false;
    protected $fillable=['name','eng_name'];
   public function products()
{
    return $this->hasManyThrough(
        Product::class,       // الجدول النهائي (products)
        Shop_product::class,   // الجدول الوسيط (shop_products)
        'category_id',        // المفتاح الأجنبي في shop_products اللي يربط بـ categories
        'id',                 // المفتاح الأساسي في products
        'id',                 // المفتاح الأساسي في categories
        'product_id'          // المفتاح الأجنبي في shop_products اللي يربط بـ products
    );
}

    public function children()
    {
        return $this->hasMany(Category::class,'parent_id');
    }

    public function parents()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    public static function countCategory(Category $category)
    {
        $countCategory=$category->children->count();
        return $countCategory;
    }
	public static function boot() {
        parent::boot();
        static::saving(function () {
            Cache::flush();
			
        } );
        static::updating(function () {
            Cache::flush();
        } );
        static::deleting(function(){Cache::flush();});
    }
}
