<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{ // تابع لاظهار السلة 
    public function index()
    {
        $cart = session()->get('cart');
        if ($cart == null)
            $cart = [];

        return view('website.checkout', compact('cart'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'mobile'     => 'required|string|max:20',
            'address'    => 'required|string|max:500',
            'city'       => 'nullable|string|max:255',
            'country'    => 'nullable|string|max:255',
            'zipcode'    => 'nullable|string|max:20',
            'order_notes' => 'nullable|string|max:500',
            // أضف تحققًا إضافيًا إذا أردت
        ]);

        $cart = session()->get('cart');

        if (!$cart || count($cart) === 0) {
            session()->flash('error', 'Your shopping cart is empty.');
            return redirect()->back();
        }

        $total_price = 0;
        $total_quantity = 0;

        foreach ($cart as $item) {
            $total_price += $item['price'] * $item['quantity'];
            $total_quantity += $item['quantity'];
        }
 // order  الطلب بللمنتج
        $order = new Order;
        $order->user_id         = auth()->user()->id;
        $order->first_name      = $request->first_name;
        $order->last_name       = $request->last_name;
        $order->email           = $request->email;
        $order->mobile          = $request->mobile;
        $order->address         = $request->address;
        $order->company         = $request->company ?? null;
        $order->city            = $request->city ?? null;
        $order->country         = $request->country ?? null;
        $order->zipcode         = $request->zipcode ?? null;
        $order->order_notes     = $request->order_notes ?? null;
        $order->total_quantity  = $total_quantity;
        $order->total_price     = $total_price;
        $order->save();

        // Save cart to database for Apriori analysis
        $dbCart = new Cart();
        $dbCart->user_id = Auth::id();
        $dbCart->status = 'completed';
        $dbCart->total = $total_price;
        $dbCart->save();
//  $order_item  منتجات الطلب 
        foreach ($cart as $item) {
            $order_item = new OrderItem;
            $order_item->order_id   = $order->id;
            $order_item->product_id = $item['id'];
            $order_item->quantity   = $item['quantity'];
            $order_item->unit_price = $item['price'];
            $order_item->save();

            // Also save to CartItem table for Apriori analysis
            $cartItem = new CartItem();
            $cartItem->cart_id = $dbCart->id;
            $cartItem->shop_product_id = $item['id'];
            $cartItem->quantity = $item['quantity'];
            $cartItem->price = $item['price'];
            $cartItem->save();
        }

        // Clear cart (session + pending saved cart)
        CartService::clearForLogout(Auth::id());

        session()->flash('success', 'Your order has been placed successfully!');
        return redirect()->route('user.order'); // أو أي صفحة تأكيد أخرى
    }

    // public function store()
    // {
    //     $total_price = 0;
    // 	$total_quantity=0;
    //     $cart = session()->get('cart');

    // 	if ($cart != null){
    //     foreach ($cart as $item)
    // 	{ $total_price += $item['price'] * $item['quantity'];
    // 	 $total_quantity +=$item['quantity'];
    // 	}
    //         $order = new Order;
    //         $order->user_id = Auth::user()->id;
    // 		$order->total_quantity=$total_quantity;
    // 		$order->total_price=$total_price;
    //         $order->save();
    //         foreach ($cart as $item) {
    // 			//dd($item['id']);
    //             $order_item = new OrderItem;
    //             $order_item->order_id = $order->id;
    //             $order_item->quantity = $item['quantity'];
    //             $order_item->product_id = $item['id'];
    // 			$order_item->unit_price = $item['price'];
    // 			$order_item->save();
    //         }
    // 		 session ()->forget ('cart');
    // 		//Session()->flush();
    // 				session()->flash('success', 'order added successfully');
    //     }else{
    // 	session()->flash('error', 'your Cart shopping in empty');}
    // 	return redirect()->back();

    // }
}
