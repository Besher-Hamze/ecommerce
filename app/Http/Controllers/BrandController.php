<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Shop_product;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BrandController extends Controller
{
    //
    public function index()
    {
        $brands = Brand::all();
        return view('dashboard.brands.index', ['brands' => $brands]);
    }

    public function create()
    {
        return view('dashboard.brands.create');
    }
    public function store(Request $request)
    {
       
        $brand = new Brand;
        $brand->name = $request->name;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('uploads/brands/', $filename);
            $brand->image = $filename;
        }
        if ($brand->save()) {
            return response()->json([
                'success' => true,
                'msg' => 'تمت الاضافة بنجاح'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'هناك خطأ في عملية الاضافة'
            ]);
        }
    }
    //edit function
    public function update($id)
    {
        $brand=Brand::findOrFail($id);
        return view('dashboard.brands.update',compact('brand'));
    }
    //update
    public function update_save(Request $request,$id)
    {
        $brand=Brand::findOrFail($id);
        $brand->name = $request->name;
        if ($request->hasFile('image')) {
            $path = 'uploads/brand/' . $brand->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('uploads/brands/', $filename);
            $brand->image = $filename;
        }
        if ($brand->update()) {
            return response()->json([
                'success' => true,
                'msg' => 'تم التعديل  بنجاح'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'هناك خطأ في عملية التعديل'
            ]);
        }

    }
    public function delete($id)
    {
        $brand=Brand::findOrFail($id);
        if($brand->delete())
        return response()->json([
            'message' => 'تم حذف القسم بنجاح', 'status' => 'ok'
         ]);
         else
         return response()->json([
            'message' => 'هناك مشكلة في عملية الحذف', 'status' => 'no'
         ]);
    }
	public function products($id)
	{
		$products=Product::join('shop_products','products.id','=','shop_products.product_id')->where('shop_products.brand_id','=',$id)
			->where('shop_id','=',1)->paginate(20);
		return view('search',compact('products'));
	}
    public function homeBrands()
    {
        $brands = Brand::paginate(10);
        return view('brands',['brands' => $brands]);
    }
}
