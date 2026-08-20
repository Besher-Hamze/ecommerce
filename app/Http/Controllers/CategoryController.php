<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryFormRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\json;

class CategoryController extends Controller
{
   public function show_cat_product($category_id)
   {
      $products = $category_id->products();
      dd($products);
   }
   public function index()
   {
      $categories = Category::all();
	   foreach($categories as $category) {
             $name1 = $category->name;
             $cat = $category;
             while ($cat->parent_id != Null) {
                $name1 = $cat->parents->name . " -> " . $name1;
                $cat = $cat->parents;
              }
              $category->name1=$name1;
         }
      return view('dashboard.categories.index', compact('categories'));
   }
   public function create()
   {      $categories = Category::all();

	     foreach($categories as $category) {
             $name1 = $category->name;
             $cat = $category;
             while ($cat->parent_id != Null) {
                $name1 = $cat->parents->name . " -> " . $name1;
                $cat = $cat->parents;
                
              }
              $category->name1=$name1;
         }
      return view('dashboard.categories.create',compact('categories'));
   }
   public function store(CategoryFormRequest $request)
   {
      $category = new Category;
      $category->name = $request['name'];
      $category->description = $request['description'];
      $category->meta_title = $request['meta_title'];
      $category->meta_keyword = $request['meta_keyword'];
      $category->meta_description = $request['meta_description'];
      $category->eng_name = $request['eng_name'];
      $category->eng_description = $request['eng_description'];
      $category->eng_meta_title = $request['eng_meta_title'];
      $category->eng_meta_keyword = $request['eng_meta_keyword'];
      $category->eng_meta_description = $request['eng_meta_description'];
      if ($request->hasFile('image')) {
         $file = $request->file('image');
         $ext = $file->getClientOriginalExtension();
         $filename = time() . '.' . $ext;
         $file->move('uploads/category/', $filename);
         $category->image_url = $filename;
      }
      $category->status = $request->status  == true ? '1' : '0';
      $category->parent_id = $request->input('parent_id');
      if ($category->save()) {
         return response()->json(['msg' => 'تمت الاضافة بنجاح', 'success' => true]);
      } else {
         return response()->json(['msg' => 'هناك خطأ في عملية الاضافة', 'success' => false]);
      }
   }
   public function update($id)
   { $categories = Category::all();

	     foreach($categories as $category) {
             $name1 = $category->name;
             $cat = $category;
             while ($cat->parent_id != Null) {
                $name1 = $cat->parents->name . " -> " . $name1;
                $cat = $cat->parents;
              }
              $category->name1=$name1;
         }
      $category = Category::findOrFail($id);
      return view('dashboard.categories.update', compact('category','categories'));
   }
   public function update_save(CategoryFormRequest $request, $id)
   {
      
      $category = Category::findOrFail($id);
      $category->name = $request['name'];
      $category->description = $request['description'];
      $category->meta_title = $request['meta_title'];
      $category->meta_keyword = $request['meta_keyword'];
      $category->meta_description = $request['meta_description'];
      $category->eng_name = $request['eng_name'];
      $category->eng_description = $request['eng_description'];
      $category->eng_meta_title = $request['eng_meta_title'];
      $category->eng_meta_keyword = $request['eng_meta_keyword'];
      $category->eng_meta_description = $request['eng_meta_description'];
      if ($request->hasFile('image')) {
         $path = 'uploads/category/' . $category->image;
         if (File::exists($path)) {
            File::delete($path);
         }
         $file = $request->file('image');
         $ext = $file->getClientOriginalExtension();
         $filename = time() . '.' . $ext;
         $file->move('uploads/category/', $filename);
         $category->image_url = $filename;
      }
      $category->status = $request->status  == true ? '1' : '0';
      $category->parent_id = $request->input('parent_id');
      if ($category->update()) {
         return response()->json(['msg' => 'تمت عملية التعديل بنجاح', 'success' => true]);
      } else {
         return response()->json(['msg' => 'هناك خطأ في عملية التعديل', 'success' => false]);
      }
   }
   public function delete($id)
   {
      $category = Category::findOrFail($id);
      $countProducts = $category->products()->count();
      $countCategory = Category::countCategory($category);
      if ($countCategory == 0 && $countProducts == 0) {
         $category->delete();
         return response()->json([
            'message' => 'تم حذف القسم بنجاح', 'status' => 'ok'
         ]);
      } else {
         return response()->json([
            'message' => 'عذرا لا يمكن حذف هذا القسم', 'status' => 'fail'
         ]);
      }
   }
}
