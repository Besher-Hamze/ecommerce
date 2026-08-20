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
                                        <h1>كوبونات</h1>
                                    </li>
                                    <li><a>البداية</a>
                                    </li>
                                    <li><a>إضافة coupon</a>
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
                                            إضافة coupon
                                        </header>
                                        <!--tab nav start-->
                                        <section class="panel">

                                            <form class="form-horizontal tasi-form" method="post" id="myForm"
                                                action="{{ route('coupon.store') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="panel-body">
                                                    <div class="tab-content">
                                                        <div id="home" class="tab-pane active">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">اسم الشركة
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" name="name" id="name"
                                                                        class="form-control req" />
                                                                    <p class="help-block"></p> 
                                                                </div>
                                                            </div>
                                                            <div class="form-group last">
                                                                <label class="control-label col-md-3">صورة الشركة</label>
                                                                <div class="col-md-9">
                                                                    <div class="fileupload fileupload-new"
                                                                        data-provides="fileupload">
                                                                        <div class="fileupload-new thumbnail"
                                                                            style="width: 200px; height: 150px;">
                                                                            <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image"
                                                                                alt="" />
                                                                        </div>
                                                                        <div class="fileupload-preview fileupload-exists thumbnail"
                                                                            style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                                                        </div>
                                                                        <div>
                                                                            <span class="btn btn-white btn-file">
                                                                                <span class="fileupload-new"><i
                                                                                        class="fa fa-paper-clip"></i>
                                                                                    اختر صورة</span>
                                                                                <span class="fileupload-exists"><i
                                                                                        class="fa fa-undo"></i>
                                                                                    تغيير</span>
                                                                                <input type="file" class="default"
                                                                                    name="image" id="image" />
                                                                            </span>
                                                                            <a href="#"
                                                                                class="btn btn-danger fileupload-exists"
                                                                                data-dismiss="fileupload"><i
                                                                                    class="fa fa-trash"></i> إزالة</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="text-center">
                                                                <button type="submit" class="btn btn-success btn-lg "
                                                                    id="save">حفظ</button>
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
        <!-- page end-->
    </section>

    <!--main content end-->
@endsection
@section('script')
    <script src="{{ asset('my_js\brand_submit.js') }}"></script>
@endsection
