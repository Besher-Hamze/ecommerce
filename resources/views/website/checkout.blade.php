@extends('website.master')
@section('content')
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Checkout</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Pages</a></li>
        <li class="breadcrumb-item active text-white">Checkout</li>
    </ol>
</div>
<!-- Single Page Header End -->


<!-- Checkout Page Start -->
<div class="container-fluid py-5">

    <div class="container py-5">
        <h1 class="mb-4">Billing details</h1>
        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="row g-5">
                <div class="col-md-12 col-lg-6 col-xl-7">
                    <div class="row">
                        <div class="col-md-12 col-lg-6">
                            <div class="form-item w-100">
                                <label class="form-label my-3">First Name<sup>*</sup></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-6">
                            <div class="form-item w-100">
                                <label class="form-label my-3">Last Name<sup>*</sup></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-item">
                        <label class="form-label my-3">Address <sup>*</sup></label>
                        <input type="text" name="address" class="form-control" placeholder="House Number Street Name" required>
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Town/City</label>
                        <input type="text" name="city" class="form-control">
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Country</label>
                        <input type="text" name="country" class="form-control">
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Postcode/Zip</label>
                        <input type="text" name="zipcode" class="form-control">
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Mobile<sup>*</sup></label>
                        <input type="tel" name="mobile" class="form-control" required>
                    </div>
                    <div class="form-item">
                        <label class="form-label my-3">Email Address<sup>*</sup></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>


                    <div class="form-item my-3">
                        <textarea name="order_notes" class="form-control" rows="5" placeholder="Order Notes (Optional)"></textarea>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6 col-xl-5">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $subtotal = 0; @endphp
                                @foreach($cart as $item)
                                <tr>
                                    <td>
                                        <img src="{{$item['image']}}" class="img-fluid rounded-circle" onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';" style="width: 60px; height: 60px;">
                                    </td>
                                    <td class="py-4">{{ $item['name'] }}</td>
                                    <td class="py-4">${{ number_format($item['price'], 2) }}</td>
                                    <td class="py-4">{{ $item['quantity'] }}</td>
                                    <td class="py-4">
                                        ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                        @php $subtotal += $item['price'] * $item['quantity']; @endphp
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Subtotal</td>
                                    <td>${{ number_format($subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Shipping</td>
                                    <td>$0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold text-uppercase">Total</td>
                                    <td>${{ number_format($subtotal, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center pt-4">
                        <button type="submit" class="btn btn-primary w-100 py-3 text-uppercase">Place Order</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>
<!-- Checkout Page End -->
@endsection