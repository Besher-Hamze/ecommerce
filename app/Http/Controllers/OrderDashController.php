<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shop_product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class OrderDashController extends Controller
{
    public function index()
	{ $orders=Order::join('users','users.id','=','orders.user_id')->select('orders.id','users.name','orders.created_at','orders.state','orders.total_price','orders.total_quantity')->orderBy('orders.created_at','desc')->get();
	 return view('dashboard.order.index',compact('orders'));
	}
	public function show(Order $order)
	{
		/*$order_items=OrderItem::join('shop_products','shop_products.id','=','order_items.product_id')
			                    ->join('products','products.id','=','shop_products.product_id')
			                    ->leftjoin('categories','categories.id','=','shop_products.category_id')
			                    ->leftjoin('brands','brands.id','=','shop_products.brand_id')
			                    ->leftjoin('shops','shops.id','=','shop_products.shop_id')
			->select('','','','','','','','','',)*/
		$order_items=OrderItem::join('products','products.id','=','order_items.product_id')
		    ->leftJoin('shop_products','shop_products.id','=','order_items.shop_product_id')
			->where('order_items.order_id','=',$order->id)
			->select(
				DB::raw('COALESCE(shop_products.id, products.id) as id'),
				'products.name',
				'products.image_url',
				'products.product_code',
				'order_items.unit_price',
				'order_items.quantity',
				'order_items.rec_source'
			)->get();
		//dd($order_items);
		return view('dashboard.order.details',compact('order','order_items'));
	}
	public function OrderProductDetail($product)
	{
		$detail=Shop_product::join('products','products.id','=','shop_products.product_id')
			                    ->leftjoin('order_items','shop_products.id','=','order_items.product_id')
			                    ->leftjoin('categories','categories.id','=','shop_products.category_id')
			                    ->leftjoin('brands','brands.id','=','shop_products.brand_id')
			                    ->leftjoin('shops','shops.id','=','shop_products.shop_id')
			                    ->select('shop_products.id','products.name','products.image_url','order_items.unit_price','products.description','products.product_code','brands.name as brand_name','shops.name as shop_name','categories.name as category')->where('shop_products.id',$product)->with('images')->first();
		//dd($detail);
		return view('dashboard.order.product_details',compact('detail'));
	}
	public function changeState(Request $request,Order $order)
	{
		if($request->approved)
		{
	     $order->state=1;
		 $order->update();	
		 session()->flash('success', 'تمت عملية تثبيت الطلب بنجاح');}
	 if($request->cancele)
	 {  $order->state=2;
		$order->update();
        session()->flash('success', 'تمت عملية إلغاء الطلب بنجاح');}
		return redirect()->back();
	}
}
