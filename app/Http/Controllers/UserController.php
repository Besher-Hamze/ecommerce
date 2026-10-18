<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('dashboard.user.index', compact('users'));
    }
    public function create()
    {
        return view('dashboard.user.create');
    }
    public function store(Request $request)
    {
        // Validate the input and return correct response
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['integer']
        ]);
        if ($validator->fails()) {
            // return response()->json($validator->errors(), 404);
            return Response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);
            return Response()->json(array(
                'success' => true,
                'msg' => "تمت إضافة المستخدم بنجاح "
            ));
        }
    }
    public function update($id)
    {
        $user = User::find($id);
        return view('dashboard.user.update', compact('user'));
    }
    public function update_save(Request $request, $id)
    {
        // Validate the input and return correct response

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['integer']
        ]);
        if ($validator->fails()) {
            // return response()->json($validator->errors(), 404);
            return Response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400);
        } else {
            User::findOrfail($id)->update([
                'name' => $request->name,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);
            return Response()->json(array(
                'success' => true,
                'msg' => "تمت عملية التعديل بنجاح "
            ));
        }
    }
    public function delete($id)
    {
        $user=User::findOrFail($id);
        if($user->role != 1)
        {
            $user->delete();
            return Response()->json(array(
                'status' => 'ok',
                'message' => "تمت عملية الحذف بنجاح "
            ));
        }
        else
        {
            return Response()->json(array(
                'status' => 'no',
                'message' => "عذرا أنت لا تستطيع حذف هذا العنصر"
            ));
        }
    }
    //  مشان يجلب كل طلبات هالمستخدم 
	public function userOrders()
	{
		$orders=Order::where('orders.user_id','=',auth()->user()->id)->orderBy('orders.created_at','desc')->paginate(10);
		//dd($orders);
		return view('website.customer-orders',compact('orders'));
	}
    //  العين الاولى ليجبلي تفاصيل كل طلب 
	public function orderDetails($id)
	{
		$details=Order::join('order_items','orders.id','=','order_items.order_id')
			           ->join('products','products.id','=','order_items.product_id')
			           ->where('orders.id','=',$id)
			           ->where('orders.user_id','=',auth()->id())
			           ->select('products.name','products.eng_name','products.id','products.image_url','order_items.quantity','order_items.unit_price','products.product_code','order_items.rec_source')
			           ->get();
		return view('website.order-details',compact('details'));
	}
}
