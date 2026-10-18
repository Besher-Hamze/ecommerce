<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $categoryId = (int) $request->input('category_id', 0);
        $brandId = (int) $request->input('brand_id', 0);
        $minPrice = $request->filled('min_price') ? (float) $request->input('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->input('max_price') : null;
        $minRating = $request->filled('min_rating') ? (float) $request->input('min_rating') : null;
        $search = trim((string) $request->input('search', ''));
        $sort = $request->input('sort', 'popular');

        $query = Product::catalog()->where('shop_products.status', 1);

        if ($categoryId > 0) {
            $childIds = Category::where('parent_id', $categoryId)->pluck('id')->all();
            $ids = array_merge([$categoryId], $childIds);
            $query->whereIn('shop_products.category_id', $ids);
        }

        if ($brandId > 0) {
            $query->where('shop_products.brand_id', $brandId);
        }

        if ($minPrice !== null) {
            $query->where('shop_products.price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('shop_products.price', '<=', $maxPrice);
        }
        if ($minRating !== null) {
            $query->where('products.rating_avg', '>=', $minRating);
        }

        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('products.name', 'ilike', $like)
                    ->orWhere('products.eng_name', 'ilike', $like)
                    ->orWhere('products.product_code', 'ilike', $like)
                    ->orWhere('products.description', 'ilike', $like)
                    ->orWhere('brands.name', 'ilike', $like);
            });
        }

        match ($sort) {
            'price_asc' => $query->orderBy('shop_products.price'),
            'price_desc' => $query->orderByDesc('shop_products.price'),
            'rating' => $query->orderByDesc('products.rating_avg')->orderByDesc('products.rating_count'),
            'newest' => $query->orderByDesc('products.created_at'),
            'name' => $query->orderBy('products.eng_name'),
            default => $query->orderByDesc('products.sales_count')->orderByDesc('products.rating_avg'),
        };

        $products = $query->paginate(16)->appends($request->query());

        $brands = Brand::orderBy('name')->get(['id', 'name']);
        $categories = Category::whereNull('parent_id')->where('status', 1)
            ->with(['children' => fn ($q) => $q->where('status', 1)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('website.all-products', compact(
            'products', 'brands', 'categories', 'categoryId', 'brandId',
            'minPrice', 'maxPrice', 'minRating', 'search', 'sort'
        ));
    }
}
