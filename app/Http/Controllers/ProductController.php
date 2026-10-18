<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFormRequest;
use App\Models\Review;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Image;
use App\Models\Shop;
use App\Models\Product_state;
use App\Models\Shop_product;
use App\Models\ProductView;
use App\Services\ProductStats;
use App\Services\Recommendation\EventTracker;
use App\Services\Recommendation\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;

class ProductController extends Controller
{
   public function import(Request $request)
   {
      set_time_limit(0); // إزالة حد وقت التنفيذ
      $file = $request->file('file');
      $path = $file->store('uploads');

      $chunkSize = 100; // عدد الصفوف في كل دفعة، يمكنك تعديل الرقم حسب حجم الملف

      // استيراد الملف باستخدام chunks
      (new \Rap2hpoutre\FastExcel\FastExcel)->import(storage_path('app/' . $path), function ($line) {

         // 1️⃣ التحقق من category
         $category = Category::firstOrCreate(
            ['name' => $line['category'] ?? 'غير محدد'],
            ['eng_name' => $line['category'] ?? 'غير محدد']
         );

         // 2️⃣ التحقق من brand
         $brand = Brand::firstOrCreate(
            ['name' => $line['brand'] ?? 'غير محدد']
         );

         // 3️⃣ إنشاء سجل في جدول products
         $product = Product::create([
            'name'             => $line['name'] ?? '',
            'description'      => $line['description'] ?? '',
            'eng_name'         => $line['name'] ?? '',
            'eng_description'  => $line['description'] ?? '',
            'image_url'        => $line['image_link'] ?? '',
         ]);

         // 4️⃣ التحقق من price
         $price = isset($line['price']) && is_numeric($line['price']) ? $line['price'] : 0;

         // 5️⃣ إنشاء سجل في جدول shop_products
         // تخزين المنتجات وربطن مع الاصناف 
         Shop_product::create([
            'product_id'     => $product->id,
            'price'          => $price,
            'product_colors' => $line['product_colors'] ?? '',
            'product_type'   => $line['product_type'] ?? '',
            'category_id'    => $category->id,
            'brand_id'       => $brand->id,
         ]);
      }, null, null, $chunkSize); // هنا حددنا حجم chunk

      return back()->with('success', 'تم استيراد المنتجات بنجاح ✅');
   }

   public function allProducts(Category $category)
   {

      $cart = session()->get('cart');
      if ($cart == null)
         $cart = [];
      $products = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
         ->select('products.*', 'shop_products.price', 'shop_products.id as shop_product_id')
         ->where('shop_products.category_id', '=', $category->id)
         ->where('shop_id', '=', 1)->paginate(12);
      //dd($products);
      return view('website.all-products', compact('products', 'category'))->with('cart', $cart);
   }
   public function showDetail($id, RecommendationService $reco)
   {
      $product = Shop_product::join('products', 'products.id', '=', 'shop_products.product_id')
         ->leftJoin('brands', 'brands.id', '=', 'shop_products.brand_id')
         ->leftJoin('categories', 'categories.id', '=', 'shop_products.category_id')
         ->where('products.id', '=', $id)
         ->select(
            'products.name',
            'shop_products.product_colors',
            'products.id as product_id',
            'products.product_code',
            'products.image_url',
            'products.description',
            'products.eng_name',
            'products.eng_description',
            'products.rating_avg',
            'products.rating_count',
            'products.form',
            'shop_products.price',
            'shop_products.id',
            'shop_products.product_type',
            'brands.name as brand_name',
            'categories.eng_name as category_name',
            'categories.id as category_id'
         )
         ->with(['images', 'reviews.user'])
         ->firstOrFail();

      $rating = (int) round($product->rating_avg ?? 0);

      ProductView::create([
         'user_id' => Auth::id(),
         'session_id' => session()->getId(),
         'product_id' => (int) $id,
         'source' => request('rec'),
      ]);
      DB::table('products')->where('id', $id)->increment('view_count');

      if ($source = request('rec')) {
         EventTracker::record('click', $source, (int) $id, request()->filled('ctx') ? (int) request('ctx') : null);
      }

      $bundle = $reco->crossSell((int) $id, 3);
      $upsells = $reco->upsell((int) $id, 2);
      $similar = $reco->similar((int) $id, 8);
      $alsoLiked = $reco->alsoLiked((int) $id, 8);
      $recentlyViewed = $reco->recentlyViewed(Auth::id(), session()->getId(), 6, (int) $id);

      EventTracker::impressions('bundle', $bundle, (int) $id);
      EventTracker::impressions('upsell', $upsells, (int) $id);
      EventTracker::impressions('similar', $similar, (int) $id);
      EventTracker::impressions('cf', $alsoLiked, (int) $id);

      return view('website.details', compact(
         'product', 'rating', 'bundle', 'upsells', 'similar', 'alsoLiked', 'recentlyViewed'
      ));
   }

   public function reviewStore(Request $request)
   {
      $data = $request->validate([
         'product_id' => ['required', 'exists:products,id'],
         'review' => ['nullable', 'string', 'max:5000'],
         'rating' => ['required', 'integer', 'between:1,5'],
      ]);

      Review::create([
         'product_id' => $data['product_id'],
         'user_id' => Auth::id(),
         'review' => $data['review'] ?? null,
         'star' => $data['rating'],
      ]);

      ProductStats::refreshRating((int) $data['product_id']);

      return redirect()->back()->with('success', 'Your review has been submitted successfully.');
   }
   public function index(Request $request)
   {
      $search = $request->search;
      $category_id = $request->category_id;
      $brand_id = $request->brand_id;
      $shop_id = $request->shop_id;
      $shops = Shop::all();
      $brands = Brand::all();
      $categories = Category::orderBy('parent_id')->get();
      foreach ($categories as $category) {
         $name1 = $category->name;
         $cat = $category;
         while ($cat->parent_id != Null) {
            $name1 = $cat->parents->name . " -> " . $name1;
            $cat = $cat->parents;
         }
         $category->name1 = $name1;
      }
      $products = Product::join('shop_products', 'products.id', '=', 'shop_products.product_id')
         ->orderBy('shop_products.created_at', 'desc');
      if ($category_id != 0) {
         $products = $products->Where('shop_products.category_id', '=', $category_id);
      }
      if ($brand_id != 0) {
         $products = $products->Where('shop_products.brand_id', '=', $brand_id);
      }
      if ($shop_id != 0) {
         $products = $products->Where('shop_products.shop_id', '=', $shop_id);
      }

      if ($search) {
         $products = $products->where(function ($products) use ($search) {
            $products->Where('products.name', 'like', '%' . $search . '%')
               ->orWhere('products.product_code', 'like', '%' . $search . '%')
               ->orWhere('products.description', 'like', '%' . $search . '%');
         });
      }
      if ($search) {
         $products = $products->where(function ($products) use ($search) {
            $products->Where('products.name', 'like', '%' . $search . '%')
               ->orWhere('products.product_code', 'like', '%' . $search . '%')
               ->orWhere('products.description', 'like', '%' . $search . '%');
         });
      }
      $products = $products->paginate(10);
      $products->appends(array('search' => $search, 'category_id' => $category_id, 'shop_id' => $shop_id, 'brand_id' => $brand_id));
      //dd($products);
      return view('dashboard.product.index', compact('products', 'categories', 'brands', 'shops', 'category_id', 'shop_id', 'brand_id', 'search'));
   }
   public function create()
   {
      $brands = Brand::all();
      $categories = Category::orderBy('parent_id')->get();
      foreach ($categories as $category) {
         $name1 = $category->name;
         $cat = $category;
         while ($cat->parent_id != Null) {
            $name1 = $cat->parents->name . " -> " . $name1;
            $cat = $cat->parents;
         }
         $category->name1 = $name1;
      }
      $products_barcode = Product::select('product_code')->get();
      $shops = Shop::all();
      return view('dashboard.product.create', compact('brands', 'categories', 'products_barcode', 'shops'));
   }
   public function store(ProductFormRequest $request)
   {
      $product = Product::where('product_code', 'like', $request->product_code)->first();
      if ($product == Null) {
         $product = new Product;
         $product->name = $request['name'];
         $product->description = $request['description'];
         $product->meta_title = $request['meta_title'];
         $product->meta_keyword = $request['meta_keyword'];
         $product->meta_description = $request['meta_description'];
         $product->eng_name = $request['eng_name'];
         $product->eng_description = $request['eng_description'];
         $product->eng_meta_title = $request['eng_meta_title'];
         $product->eng_meta_keyword = $request['eng_meta_keyword'];
         $product->eng_meta_description = $request['eng_meta_description'];
         $product->product_code = $request->product_code;
         if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('uploads/product/', $filename);
            $product->image_url = $filename;
         }

         $product->save();
      }

      $shop_product = new Shop_product;
      $shop_product->status = $request->status  == true ? '1' : '0';
      $shop_product->shipping = $request->shipping  == true ? '1' : '0';
      $shop_product->category_id = $request->input('category_id');
      $shop_product->price = $request->price;
      $shop_product->count = $request->count;
      $shop_product->brand_id = $request->brand_id;
      $shop_product->shop_id = $request->shop_id;
      $shop_product->product_id = $product->id;
      $shop_product->save();
      if ($request->hasfile('extra_image')) {

         foreach ($request->file('extra_image') as $image) {
            $name = $image->getClientOriginalName();
            $image->move(public_path() . '/uploads/product', $name);
            $new_image = new Image;
            $new_image->product_id = $shop_product->id;
            $new_image->url = $name;
            $new_image->save();
         }
      }
      if ($product->save()) {
         return response()->json(['msg' => 'تمت الاضافة بنجاح', 'success' => true]);
      } else {
         return response()->json(['msg' => 'هناك خطأ في عملية الاضافة', 'success' => false]);
      }
   }
   public function update($id)
   {
      $brands = Brand::all();
      $categories = Category::all();
      $product_states = Product_state::all();
      $shops = Shop::all();
      //$product = Product::findOrFail($id);
      $product = Shop_product::join('products', 'products.id', '=', 'shop_products.product_id')->where('shop_products.id', '=', $id)
         ->select('products.id as product_id', 'products.name', 'products.eng_name', 'products.product_code', 'products.description', 'products.eng_description', 'products.image_url', 'products.eng_meta_title', 'products.eng_meta_keyword', 'products.eng_meta_description', 'products.meta_title', 'products.meta_keyword', 'products.meta_description', 'shop_products.id', 'shop_products.price', 'shop_products.shop_id', 'shop_products.product_id', 'shop_products.category_id', 'shop_products.brand_id', 'shop_products.count', 'shop_products.shipping', 'shop_products.status')
         ->with('images')->first();
      //dd($product);
      return view('dashboard.product.update', compact('product', 'brands', 'categories', 'product_states', 'shops'));
   }
   public function update_save(ProductFormRequest $request, $id)
   {
      $product = Product::findOrFail($id);
      $product->name = $request['name'];
      $product->description = $request['description'];
      $product->meta_title = $request['meta_title'];
      $product->meta_keyword = $request['meta_keyword'];
      $product->meta_description = $request['meta_description'];
      $product->eng_name = $request['eng_name'];
      $product->eng_description = $request['eng_description'];
      $product->eng_meta_title = $request['eng_meta_title'];
      $product->eng_meta_keyword = $request['eng_meta_keyword'];
      $product->eng_meta_description = $request['eng_meta_description'];
      if ($request->hasFile('image')) {
         $path = 'uploads/product/' . $product->image_url;
         if (File::exists($path)) {
            File::delete($path);
         }
         $file = $request->file('image');
         $ext = $file->getClientOriginalExtension();
         $filename = time() . '.' . $ext;
         $file->move('uploads/product/', $filename);
         $product->image_url = $filename;
      }

      $shop_product = Shop_product::where('id', $request->old_shop_id)->first();
      $shop_product->status = $request->status  == true ? '1' : '0';
      $shop_product->shipping = $request->shipping  == true ? '1' : '0';
      $shop_product->category_id = $request->input('category_id');
      $shop_product->price = $request->price;
      $shop_product->count = $request->count;
      $shop_product->brand_id = $request->brand_id;
      $shop_product->shop_id = $request->shop_id;
      $shop_product->product_id = $product->id;
      $shop_product->update();
      $imagearray = [];
      if ($request->input('ImageId') != null) {

         $imagearray = $request->input('ImageId');
      }
      $images = Image::whereNotIn('id', $imagearray)->get();
      foreach ($images as $image) {
         File::delete(public_path('uploads/product/' . $image->url));

         $image->delete();
      }
      if ($request->hasfile('extra_image')) {

         foreach ($request->file('extra_image') as $image) {
            $name = $image->getClientOriginalName();
            $image->move(public_path() . '/uploads/product', $name);
            $new_image = new Image;
            $new_image->product_id = $shop_product->id;
            $new_image->url = $name;

            $new_image->save();
         }
      }
      if ($product->update()) {
         return response()->json(['msg' => 'تم التعديل  بنجاح', 'success' => true]);
      } else {
         return response()->json(['msg' => 'هناك خطأ في عملية التعديل', 'success' => false]);
      }
   }
   public function product_code_search($code)
   {
      $product = Product::where('product_code', 'like', $code)->first();
      return response()->json(['product' => $product]);
   }
   public function image_upload(Request $request)
   {
      $this->validate($request, [
         'file' => 'required|image|mimes:jpeg,png,jpg,gif',
      ]);

      if ($request->hasFile('file')) {
         $image = $request->file('file');
         $name = time();
         $ext = $image->getClientOriginalExtension();
         $destinationPath = public_path('app/public/product');
         $image->move($destinationPath, $name . "." . $ext);


         $success_message = array(
            'success' => 200,
            'filename' => $name . "." . $ext
         );

         return json_encode($success_message);
      }
   }
}
