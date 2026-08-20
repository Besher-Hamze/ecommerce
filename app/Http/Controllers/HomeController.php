<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop_product;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Review;
use DB;

class HomeController extends Controller
{
	//
	public function index()
	{
		$last_products = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
			->select('products.*', 'shop_products.price', 'shop_products.id as shop_product_id')
			->orderBy('shop_products.created_at', 'desc')->take(8)->get();
		$allProducts = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
			->select('products.*', 'shop_products.price', 'shop_products.id as shop_product_id')
			->orderBy('shop_products.created_at', 'desc')->take(8)->get();
		//$brands = Brand::all();
		$slider_images = Slider::all();
		$categories = Category::all();
		$reviews = Review::latest()  // ترتيب حسب الأحدث
			->take(5)   // أخذ أول 5 تعليقات فقط
			->get();
		// أفضل المنتجات مبيعاً (اعتماداً على عدد المبيعات)
		$bestSellingProducts = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
			->join('order_items', 'products.id', '=', 'order_items.product_id')
			->select(
				'products.id',
				'products.name',
				'products.description',
				'shop_products.price',
				'products.image_url',
				'shop_products.id as shop_product_id',
				DB::raw('SUM(order_items.quantity) as total_sold')
			)
			->groupBy('products.id', 'products.name', 'products.description', 'shop_products.price', 'products.image_url', 'shop_products.id')
			->orderByDesc('total_sold')
			->take(8)
			->get();



		return view('website.index', compact('last_products', 'slider_images', 'categories', 'allProducts', 'reviews', 'bestSellingProducts'));
	}
}
