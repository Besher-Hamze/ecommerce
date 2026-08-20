@extends('dashboard.master')
@section('content')
    <div>
        <!--main content start-->
        <section id="main-content" dir="rtl">
            <section class="wrapper site-min-height">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
                <!-- page start-->
                <section class="panel">
                    <header class="panel-heading">
                        Sliders </header>
                    <div class="panel-body">
                        <div class="adv-table editable-table ">
                            <div class="clearfix">
                                <div class="btn-group">
                                    <a class="btn green" href="{{ url('admin/slider/create') }}">
                                        إضافة slider جديد <i class="fa fa-plus"></i>
                                    </a>
                                </div>

                            </div>
                            <div class="space15"></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" id="editable-sample">
                                    <thead>
                                        <tr>
                                            <th>الصورة</th>
                                            <th>النص عربي</th>
                                            <th>النص EN</th>
                                            <th>url</th>
                                            <th>تعديل</th>
                                            <th>حذف</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sliders as $slider)
                                            <tr class="">
                                                <td><img src="{{ asset('uploads/slider/' . $slider->image) }}" width="60"
                                                        height="60"></td>
                                                <td>{{ $slider->text_ar }}
                                                </td>
                                                <td>{{ $slider->text_en }}
                                                </td>
                                                <td>{{ $slider->url }}</td>

                                                <td><a class=""
                                                        href="{{ route('slider.update', $slider->id) }}">تعديل</a></td>
                                                        <td style="text-align: center">
                                                            <button type="button" class="btn-primary delete"
                                                            data-route="{{ route('slider.delete', $slider->id) }}" style="border:0px;"
                                                                data-toggle="modal" data-target="#myModal3"><i
                                                                    class="glyphicon glyphicon-trash"></i>
                                                            </button>
                                                        </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal fade" id="myModal3" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-hidden="true">&times;</button>
                                            <h4 class="modal-title">حذف</h4>
                                        </div>
                                        <div class="modal-body">
                                            هل أنت متأكد من حذف هذه الصورة؟
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default clos" id="close"
                                                type="button">
                                                إلغاء
                                            </button>
                                            <button class="btn btn-warning confirm" type="button"> موافق</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- page end-->
            </section>
        </section>
    </div>
@endsection
