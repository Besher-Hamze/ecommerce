<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\ProductStats;
use App\Services\Recommendation\EventTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        if (!$cart) {
            return redirect()->route('cart.list')->with('error', 'Your shopping cart is empty.');
        }

        $totals = CartService::totals($cart);
        $user = Auth::user();

        return view('website.checkout', compact('cart', 'totals', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'order_notes' => 'nullable|string|max:500',
        ]);

        $cart = session('cart', []);
        if (!$cart) {
            return redirect()->back()->with('error', 'Your shopping cart is empty.');
        }

        $totals = CartService::totals($cart);
        $totalQuantity = CartService::totalQuantity($cart);

        DB::transaction(function () use ($request, $cart, $totals, $totalQuantity) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'company' => $request->company,
                'city' => $request->city,
                'country' => $request->country,
                'zipcode' => $request->zipcode,
                'order_notes' => $request->order_notes,
                'total_quantity' => $totalQuantity,
                'subtotal' => $totals['subtotal'],
                'shipping' => $totals['shipping'],
                'total_price' => $totals['total'],
                'state' => Order::STATE_PENDING,
            ]);

            $dbCart = Cart::create([
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'status' => 'completed',
                'total' => $totals['subtotal'],
            ]);

            foreach ($cart as $shopProductId => $item) {
                $source = $item['source'] ?? null;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'shop_product_id' => $shopProductId,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'rec_source' => $source,
                ]);

                CartItem::create([
                    'cart_id' => $dbCart->id,
                    'shop_product_id' => $shopProductId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'source' => $source,
                ]);

                if ($source) {
                    EventTracker::record('purchase', $source, (int) $item['id'], null, (float) ($item['price'] * $item['quantity']));
                }
            }
        });

        ProductStats::refresh();
        CartService::clearForLogout(Auth::id());

        return redirect()->route('user.order')->with('success', 'Your order has been placed successfully!');
    }
}
