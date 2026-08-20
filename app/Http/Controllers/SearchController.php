<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop_product;
use App\Models\Product;

class SearchController extends Controller
{ //  بحث  حسب الكلمة بجيب المفتاحية  منتج وبجيب كل المنتجات حسب الكلمة المفتاحية 
    public function search(Request $request)
    { 
        $category_id = $request->category_id;
        $search = $request->search;

        $products = Product::join('shop_products','products.id','=','shop_products.product_id')
            ->select('products.*', 'shop_products.price', 'shop_products.id as shop_product_id');

        if ($category_id != 0) {
            $products = $products->where('shop_products.category_id', $category_id);
        }

        if ($search) {
            $products->where(function ($products) use ($search) {
                $products->where('products.name', 'like', '%' . $search . '%')
                         ->orWhere('products.product_code', 'like', '%' . $search . '%')
                         ->orWhere('products.description', 'like', '%' . $search . '%')
                         ->orWhere('products.eng_name', 'like', '%' . $search . '%')
                         ->orWhere('products.eng_description', 'like', '%' . $search . '%');      
            });
        }

        //  16 بكل دفعة عرض النتائج بشكل عشوائي
        $products = $products->inRandomOrder()->paginate(16);

        // الحفاظ على قيم البحث عند التنقل بين الصفحات
        $products->appends(['search' => $search, 'category_id' => $category_id]);

        return view('website.all-products', compact('products'));
    }
}
