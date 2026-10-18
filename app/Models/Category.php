<?php

namespace App\Models;

use App\Providers\AppServiceProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $table = "categories";

    protected $fillable = ['name', 'eng_name', 'slug', 'icon', 'parent_id', 'status', 'sort_order', 'description', 'eng_description'];

    public function products()
    {
        return $this->hasManyThrough(
            Product::class,
            Shop_product::class,
            'category_id', // shop_products.category_id
            'id',          // products.id
            'id',          // categories.id
            'product_id'   // shop_products.product_id
        );
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parents()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public static function countCategory(Category $category)
    {
        return $category->children->count();
    }

    protected static function booted()
    {
        $forget = fn () => Cache::forget(AppServiceProvider::CATEGORIES_CACHE_KEY);
        static::saved($forget);
        static::deleted($forget);
    }
}
