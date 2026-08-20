@extends('dashboard.master')
@section('content')
    <style>
        .form-horizontal .control-label {
            font-weight: bold;
            font-size: 14px;
            text-align: right;
            padding: 0 40px;
        }
    </style>
    <!--  summernote -->
    <head>
        <link href="{{ asset('dashboard/assets/summernote/dist/summernote.css') }}" rel="stylesheet">
    </head>
    <!---->
    <section id="container" class="">
        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <ul class="breadcrumb">
                                    <li>
                                        <h1>Slider</h1>
                                    </li>
                                    <li><a>البداية</a>
                                    </li>
                                    <li><a>تعديل Slider</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- page start-->
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <!-- page start-->
                            <div class="row">
                                <div class="col-lg-12">
                                    <section class="panel">
                                        <header class="panel-heading">
                                            تعديل Slider
                                        </header>
                                        <!--tab nav start-->
                                        <section class="panel">
                                            <header class="panel-heading tab-bg-dark-navy-blue ">
                                                <ul class="nav nav-tabs">
                                                    <li class="active">
                                                        <a data-toggle="tab" href="#home">البيانات باللغة الانكليزية</a>
                                                    </li>
                                                    <li class="">
                                                        <a data-toggle="tab" href="#about">البيانات باللغة العربية</a>
                                                    </li>
                                                    <li class="">
                                                        <a data-toggle="tab" href="#profile">بيانات أخرى</a>
                                                    </li>
                                                </ul>
                                            </header>
                                            <form class="form-horizontal tasi-form" method="post" id="myForm"
                                                action="{{ route('slider.update_save', $slider->id) }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="panel-body">
                                                    <div class="tab-content">
                                                        <div id="home" class="tab-pane active">
                                                            <div class="form-group">
                                                                <label class="col-lg-2 col-sm-2 control-label">الوصف
                                                                </label>
                                                                <div class="panel-body col-lg-10">
                                                                    <div class="summernote " id="eng_summernote">{!!$slider->text_en!!}
                                                                    </div>
                                                                    <textarea style="display:none;" class="req" name="text_en" id="text_en"></textarea>
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="about" class="tab-pane">
                                                            
															<div class="form-group">
                                                                <label class="col-lg-2 col-sm-2 control-label">الوصف
                                                                </label>
                                                                <div class="panel-body col-lg-10">
                                                                    <div class="summernote " id="summernote">
                                                                    {!!$slider->text_ar!!}</div>
                                                                    <textarea style="display:none;" class="req" name="text_ar" id="text_ar"></textarea>
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="profile" class="tab-pane">
                                                            <div class="form-group last">
                                                                <label class="control-label col-md-3">صورة </label>
                                                                <div class="col-md-9">
                                                                    <div class="fileupload fileupload-new"
                                                                        data-provides="fileupload">
                                                                        <div class="fileupload-new thumbnail"
                                                                            style="width: 200px; height: 150px;">
                                                                            <img src="{{asset('uploads/slider/'. $slider->image)}}"
                                                                                alt="" />
                                                                        </div>
                                                                        <div class="fileupload-preview fileupload-exists thumbnail"
                                                                            style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                                                        </div>
                                                                        <div>
                                                                            <span class="btn btn-white btn-file">
                                                                                <span class="fileupload-new"><i
                                                                                        class="fa fa-paper-clip"></i>
                                                                                    اختر صورة </span>
                                                                                <span class="fileupload-exists"><i
                                                                                        class="fa fa-undo"></i>
                                                                                    تغيير</span>
                                                                                <input type="file" class="default "
                                                                                    name="image"
                                                                                    accept=".jpg,.png,.jpeg,.gif,.svg" />
                                                                            </span>
                                                                            <a href="#"
                                                                                class="btn btn-danger fileupload-exists"
                                                                                data-dismiss="fileupload"><i
                                                                                    class="fa fa-trash"></i> إزالة</a>
            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">url
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" name="url"
                                                                        class="form-control req" id="url" value="{{$slider->url}}"/>
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <!--main content end-->
                                                                    <div class="text-center">
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-lg "
                                                                            id="update">حفظ</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </section>
                                    </section>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </section>
    </section>
    <!--main content end-->
@endsection
@section('script')
    <script src="{{ asset('my_js\slider_submit.js') }}"></script>
@endsection
