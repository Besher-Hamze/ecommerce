
@extends('website.master')
@section('content')
<div class="container">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
</div>
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Cart</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Pages</a></li>
        <li class="breadcrumb-item active text-white">Cart</li>
    </ol>
</div>
<!-- Single Page Header End -->



<!-- Cart Page Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                            <th>{{__('order.order_date')}}</th>
                            <th>{{__('order.state')}}</th>
                            <th>{{__('order.qty')}}</th>
                            <th>{{__('order.total_price')}}</th>
                            <th>{{__('order.show_detail')}}</th>
                        </tr>
                </thead>
                <tbody class="align-middle">
                        <?php $total = 0; ?>
                        @foreach ($orders as $order)
                            @php $total += $order->total_price @endphp
                            <tr>
                                <td class="align-middle">{{ $order->created_at }}</td>
								<td class="align-middle">{{ $order->state  == '0' ? 'Wait' : 'approverd' }}    </td>
                                <td class="align-middle" >
                                  {{ $order->total_quantity }} 
                                </td>
								<td class="align-middle"><?php echo number_format( $order->total_price);?> {{__('other.unit')}}</td>
								<td class="align-middle"><a href="{{route('order.details',$order->id)}}"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
            </table>
        </div>
        <!-- <div class="mt-5">
            <input type="text" class="border-0 border-bottom rounded me-5 py-3 mb-4" placeholder="Coupon Code">
            <button class="btn border-secondary rounded-pill px-4 py-3 text-primary" type="button">Apply Coupon</button>
        </div> -->
        <div class="row g-4 justify-content-end">
            <div class="col-8"></div>
            <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                <div class="bg-light rounded">
                    <div class="p-4">
                        <h1 class="display-6 mb-4">Cart <span class="fw-normal">Total</span></h1>
                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="mb-0 me-4">Subtotal:</h5>
                            <p class="mb-0">${{$total}}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0 me-4">Shipping</h5>
                            <div class="">
                                <p class="mb-0">Flat rate: $3.00</p>
                            </div>
                        </div>
                        <p class="mb-0 text-end">Shipping to Ukraine.</p>
                    </div>
                    <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                        <h5 class="mb-0 ps-4 me-4">Total</h5>
                        <p class="mb-0 pe-4">${{$total+3}}</p>
                    </div>
           
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Cart Page End -->
@endsection

