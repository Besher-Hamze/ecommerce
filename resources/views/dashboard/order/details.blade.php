@extends('dashboard.master')
@section('content')
      <!--main content start-->
    <section id="main-content">
        <section class="wrapper site-min-height">
            <!-- page start-->
            <section class="panel">
                <header class="panel-heading">
                 تفاصيل الطلبية 
                </header>

            </section>
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
        @yield('content')
            <div class="row">
                <div class="col-md-12">
                    <section class="panel">
                        <div class="bio-graph-heading project-heading">
                            <strong> رقم الطلبية : {{$order->id}} </strong>
                        </div>
                        <div class="panel-body bio-graph-info">
                            <!--<h1>New Dashboard BS3 </h1>-->
                            <div class="row p-details">
                                <div class="bio-row">
                                    <p><span class="bold">اسم الزبون </span>: {{$order->user->name}}</p>
                                </div>
                                <div class="bio-row">
                                    <p><span class="bold">حالة الطلب </span>: <span
                                            class="label label-primary">@switch($order->state)
@case(0)
<span> Wait</span>
@break
@case(1)
<span>approved</span>
@break
@case(2)
<span>cancele</span>
@break
@default
<span>Something went wrong, please try again</span>
@endswitch</span></p>
                                </div>
                                <div class="bio-row">
                                    <p><span class="bold">تاريخ الطلب </span>: {{$order->created_at}}</p>
                                </div>
                                <div class="bio-row">
                                    <p><span class="bold">تاريخ آخر تعديل</span>: {{$order->updated_at}}</p>
                                </div>
                                <div class="bio-row">
                                    <p><span class="bold">كمية الطلب الكليّة</span>: <a href="#">{{$order->total_quantity}}</a></p>
                                </div>
                                <div class="bio-row">
                                    <p><span class="bold">السعر الإجمالي للطلب </span>: {{$order->total_price}}</p>
                                </div>
                            </div>
							<div class="row">
							<div class="col-lg-3">	
							@if($order->state != 1)
							<form action="{{route('order.state',$order->id)}}" method="post">
								@csrf							
                                <span class="pull-right">
								<input value="approved" name="approved" type="hidden" />
                                 <button type="submit" id="loading-btn" class="btn btn-warning btn-xs"><i
                                  class="fa fa-refresh"></i> الموافقة على الطلب
									 </button>
                                </span>
						     </form>
							@endif
								</div>
							<div class="col-lg-3">
							@if($order->state != 2)
							<form action="{{route('order.state',$order->id)}}" method="post">
								@csrf
                             <input value="cancele" name="cancele" type="hidden" />
							  <span class="pull-right">
                                <button type="submit" id="loading-btn" class="btn btn-warning btn-xs"><i
                                  class="fa fa-refresh"></i>إلغاء الطلب
									 </button>
                                </span>
						   </form>
								@endif
									</div>
							</div>
                        </div>

                    </section>

                    <section class="panel">
                        <header class="panel-heading">
                           منتجات الطلبية 
                        </header>
                        <div class="panel-body">
                            <table class="table table-hover p-table">
                                <thead>
                                <tr>
									<th>صورة المنتج</th>
                                    <th>رقم المنتج </th>
                                    <th>اسم المنتج</th>
							       <th>سعر المنتج</th>
                                   <th>الكمية</th>
								   <th>تفاصيل</th>
                                </tr>
                                </thead>
                                <tbody>
								@foreach($order_items as $item)		
                                <tr>
                                    <td>
                                   <img src="{{ asset('uploads/product/' . $item->image_url) }}"
                                                                                alt=""  height="50px"/></td>
                                    <td>
                                        {{$item->product_code}}
                                    </td>
                                    <td>
                                        {{$item->name}}
                                    </td>
									 <td>
                                        {{$item->unit_price}}ل.س
                                    </td>
                                    <td>
                                        {{$item->quantity}}
                                    </td>
                                   <td>
									   <a href="{{route('order_product.detail',$item->id)}}"> <i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                                 @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>        
            </div>
            <!-- page end-->
        </section>
    </section>
    <!--main content end-->
 
@endsection
