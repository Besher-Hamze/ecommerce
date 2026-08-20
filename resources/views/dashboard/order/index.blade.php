@extends('dashboard.master')
@section('content')
    <section id="main-content">
        <section class="wrapper site-min-height">
            <!-- page start-->
            <section class="panel">
                <header class="panel-heading">
                    جدول الطلبات
                </header>
                <div class="panel-body">
                    <div class="adv-table editable-table ">
                        <div class="clearfix">
                            <div class="btn-group">
                                <a id="" class="btn green" href="{{ route('order.create') }}">
                                    إضافة طلب جديد <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="space15"></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered" id="editable-sample">
                                <thead>
                                    <tr>
                                        <th>رقم  الطلب </th>
                                        <th>اسم الزبون</th>
									    <th> الحالة</th>
                                        <th>السعر الإجمالي</th>
										 <th>تاريخ الاضافة</th>
										 <!--<th>تاريخ التعديل</th>-->									
                                         <th>عرض</th>
                                    </tr>
                                </thead>
                                <tbody id="store">
                                    @foreach ($orders as $order)
                                        <tr class="">
                                            <td>{{$order->id}}</td>
                                            <td>{{$order->name}}</td>
											<td>{{$order->state == '0' ? 'wait' : 'approved'}}</td>
											<td>{{$order->total_price}}</td>
											<td>{{$order->created_at}}</td>
											 <!--<td>{{$order->updated_at}}</td>										
                                           <td><a class="" href="{{ route('order.update', $order->id) }}">تعديل</a>
                                            </td>-->
											 <td><a class="" href="{{ route('order.show',$order->id) }}"><i
                                                        class="fa fa-eye"></i></a>
                                            </td>
                                            <!--<td style="text-align: center">
                                                <button type="button" class="btn-primary delete"
                                                    data-route="{{ route('order.destroy', $order->id) }}" style="border:0px;"
                                                    data-toggle="modal" data-target="#myModal3"><i
                                                        class="glyphicon glyphicon-trash"></i>
                                                </button>
                                            </td>-->
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"
                                    aria-hidden="true">&times;</button>
                                <h4 class="modal-title">حذف</h4>
                            </div>
                            <div class="modal-body">
                                هل أنت متأكد من حذف هذه الطلب؟
                            </div>
                            <div class="modal-footer">
                                <button data-dismiss="modal" class="btn btn-default clos" id="close" type="button">
                                    إلغاء
                                </button>
                                <button class="btn btn-warning confirm" type="button"> موافق</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- page end-->
        </section>
    </section>
    <!--main content end-->
@endsection
