@extends('dashboard.master')
@section('content')
    <section id="main-content">
        <section class="wrapper site-min-height">
            <!-- page start-->
            <section class="panel">
                <header class="panel-heading">
                    جدول الشركات
                </header>
                <div class="panel-body">
                    <div class="adv-table editable-table ">
                        <div class="clearfix">
                            <div class="btn-group">
                                <a id="" class="btn green" href="{{ route('brand.create') }}">
                                    إضافة شركة جديدة <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="space15"></div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered" id="editable-sample">
                                <thead>
                                    <tr>
                                        <th>صورة الشركة </th>
                                        <th>اسم الشركة</th>
                                        <th>تعديل</th>
                                        <th>حذف</th>
                                    </tr>
                                </thead>
                                <tbody id="store" data-route="{{ route('brand.store') }}">
                                    @foreach ($brands as $brand)
                                        <tr class="" data-routeUpdate="{{ route('brand.update_save', $brand->id) }}">
                                            <td><img src="{{ asset('uploads/brands/' . $brand->image) }}" width="60px"
                                                    height="40px" /></td>
                                            <td>{{ $brand->name }}</td>
                                            <td><a class="" href="{{ route('brand.update', $brand->id) }}">تعديل</a>
                                            </td>
                                            <td style="text-align: center">
                                                <button type="button" class="btn-primary delete"
                                                    data-route="{{ route('brand.delete', $brand->id) }}" style="border:0px;"
                                                    data-toggle="modal" data-target="#myModal3"><i
                                                        class="glyphicon glyphicon-trash"></i>
                                                </button>
                                            </td>
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
                                هل أنت متأكد من حذف هذه الشركة؟
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
