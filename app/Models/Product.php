<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "products";

    protected $casts = [
        'id' => 'integer',
        'product_code' => 'string',
        'tags' => 'array',
        'rating_avg' => 'float',
        'rating_count' => 'integer',
        'sales_count' => 'integer',
        'price' => 'float',
    ];

    protected $fillable = [
        'source_id', 'name', 'eng_name', 'description', 'eng_description', 'image_url', 'product_code',
        'form', 'tags', 'rating_avg', 'rating_count', 'sales_count', 'view_count',
    ];

    /**
     * Storefront projection: product + listing price + brand + category in one query
     * (avoids the N+1 queries the product card used to trigger).
     */
    public function scopeCatalog(Builder $query): Builder
    {
        return $query->join('shop_products', 'products.id', '=', 'shop_products.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'shop_products.brand_id')
            ->leftJoin('categories', 'categories.id', '=', 'shop_products.category_id')
            ->select(
                'products.id',
                'products.name',
                'products.eng_name',
                'products.description',
                'products.eng_description',
                'products.image_url',
                'products.form',
                'products.rating_avg',
                'products.rating_count',
                'products.sales_count',
                'products.created_at',
                'shop_products.id as shop_product_id',
                'shop_products.price',
                'shop_products.product_type',
                'shop_products.category_id',
                'shop_products.brand_id',
                'brands.name as brand_name',
                'categories.eng_name as category_name'
            );
    }

    public function shopProduct()
    {
        return $this->hasOne(Shop_product::class, 'product_id');
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_products', 'product_id', 'shop_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id')->withPivot('quantity');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id')->latest();
    }

    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            Shop_product::class,
            'product_id',
            'id',
            'id',
            'category_id'
        );
    }

    public function getImageAttribute(): string
    {
        return self::imageUrl($this->image_url);
    }

    public static function imageUrl(?string $path): string
    {
        if (!$path) {
            return asset('img/default.jpeg');
        }

        return str_starts_with($path, 'http') ? $path : asset($path);
    }
}
