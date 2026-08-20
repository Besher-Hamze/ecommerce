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
            <thead class="thead-dark">
                        <tr> <th>{{__('other.image')}}</th>
                            <th>{{__('other.products')}}</th>
                            <th>{{__('other.quantity')}}</th>
							<th>{{__('other.unit_price')}}</th>
                            <th>{{__('other.total')}}</th>
                            <th>{{__('product_details.show_detail')}}</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle">
                        <?php $total = 0; ?>
                        @foreach ($details as $detail)
						@php $total +=$detail->unit_price * $detail->quantity @endphp
                            <tr>
                                <td class="align-middle">
									<img src="{{$detail->image_url}}" alt=""
                                        style="width:50px;"></td>
								<td class="align-middle">
									                                      @if ( Config::get('app.locale') == 'en')
                                                                           {{$detail->eng_name}}
                                                                           @elseif ( Config::get('app.locale') == 'ar' )
                                                                           {{$detail->name}}
                                                                           @endif </td>
                                <td class="align-middle">{{$detail->quantity}}</td>
                                <td class="align-middle"><?php echo number_format( $detail->unit_price);?>  {{__('other.unit')}}</td>
                                <td class="align-middle"><?php echo number_format( $detail->unit_price * $detail->quantity);?>  {{__('other.unit')}}</td>
								<td class="align-middle"><a href="{{route('product.details',$detail->id)}}" ><i class="fa fa-eye"></i></a>
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
