<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Support\Facades\File;
class ShopController extends Controller
{
    //
	public function index()	
	{$shops=Shop::all();
		return view('dashboard.shops.index',compact('shops'));
	}
	public function create()
	{
		$categories=Category::whereNull('parent_id')->get();
		return view('dashboard.shops.create',compact('categories'));
	}
	public function store(Request $request)
	{
		$shop=new Shop;
		$shop->name=$request->name;
		$shop->eng_name=$request->eng_name;
		$shop->phone=$request->phone;
		$shop->city=$request->city;
		$shop->description=$request->description;
		$shop->eng_description=$request->eng_description;
		$shop->category_id=$request->category_id;
		$shop->status=$request->status  == true ? '1' : '0';
		 if ($request->hasFile('image')) {
         $file = $request->file('image');
         $ext = $file->getClientOriginalExtension();
         $filename = time() . '.' . $ext;
         $file->move('uploads/shops/', $filename);
         $shop->image = $filename;}
		 if ($shop->save()) 
         return response()->json(['msg' => 'تمت الاضافة بنجاح', 'success' => true]);
          else 
         return response()->json(['msg' => 'هناك خطأ في عملية الاضافة', 'success' => false]);
	 }
		
	
	
	public function show($id)
	{	$categories=Category::whereNull('parent_id')->get();
		$shop=Shop::findORFail($id);
		return view('dashboard.shops.update',compact('shop','categories'));
	}
	public function update_save(Request $request,$id)
	{
		$shop=Shop::find($id);
		$shop->name=$request->name;
		$shop->eng_name=$request->eng_name;
		$shop->phone=$request->phone;
		$shop->city=$request->city;
		$shop->description=$request->description;
		$shop->eng_description=$request->eng_description;
		$shop->category_id=$request->category_id;
		$shop->status=$request->status  == true ? '1' : '0';
		
		 if ($request->hasFile('image')) {
		$path = 'uploads/shops/' . $shop->image;
         if (File::exists($path)) {
            File::delete($path);
         }
         $file = $request->file('image');
         $ext = $file->getClientOriginalExtension();
         $filename = time() . '.' . $ext;
         $file->move('uploads/shops/', $filename);
         $shop->image = $filename;}
		
		 if ($shop->update()) 
		
         return response()->json(['msg' => 'تمت عملية التعديل بنجاح', 'success' => true]);
          else 
         return response()->json(['msg' => 'هناك خطأ في عملية التعديل', 'success' => false]);
	 }
		
	public function destroy($id)
	{
		//dd($shop->products->count());
		$shop=Shop::find($id);
		if($shop->products->count() == 0)
		{ $shop->delete();
	     return response()->json(['message' => 'تم الحذف بنجاح', 'status' => 'ok']);
		}else
         return response()->json(['message' => 'هناك خطأ في عملية الحذف عليك حذف المنتجات التابعة لهذا المحل أولا', 'status' => 'no']);
	}
}
