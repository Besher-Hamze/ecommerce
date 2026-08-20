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
                                        <h1>المحلات </h1>
                                    </li>
                                    <li><a>البداية</a>
                                    </li>
                                    <li><a>إضافة محل</a>
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
                                            إضافة محل
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
                                                action="{{ route('shop.store') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="panel-body">
                                                    <div class="tab-content">
                                                        <div id="home" class="tab-pane active">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">اسم المحل
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" name="eng_name" id="eng_name"
                                                                        class="form-control req" />
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
															 <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">الوصف
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <textarea type="text" name="eng_description" id="eng_description"
                                                                        class="form-control req" ></textarea>
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                      
                                                        </div>
                                                        <div id="about" class="tab-pane">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">اسم المحل
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" name="name"
                                                                        class="form-control req" id="name" />
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                           <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">الوصف
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <textarea type="text" name="description" id="description"
                                                                        class="form-control req"></textarea>
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                           
                                                        </div>
                                                        <div id="profile" class="tab-pane">
                                                            <div class="form-group last">
                                                                <label class="control-label col-md-3">صورة المحل</label>
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
                                                                                <input type="file" class="default req"
                                                                                    name="image" id="image" />
																				
                                                                            </span>																	                                                                                                                                                                                                                   <p class="help-block"></p>
                                                                            <a href="#"
                                                                                class="btn btn-danger fileupload-exists"
                                                                                data-dismiss="fileupload"><i
                                                                                    class="fa fa-trash"></i> Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                          
                                                             <div class="row">
                                                                <div class="col-lg-6">
                                                                   <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">الهاتف  
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input type="number" name="phone" id="phone"
                                                                        class="form-control " />
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                                </div>
                                                             <div class="col-lg-6">
                                                                  <div class="form-group">
                                                                <label class="col-sm-2  control-label">المكان
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" name="city" id="city"
                                                                        class="form-control " />
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
						                              	 </div>
                                                            </div>
															<hr/>
															  <div class="row">
                                                                <div class="col-lg-6">
                                                                    <label class="col-sm-2 control-label"
                                                                        name="category_id" for="inputSuccess">القسم
                                                                        </label>

                                                                    <select class="form-control m-bot15 col-sm-10"
                                                                        name="category_id" id="category_id">
                                                                        <option value>لا يوجد</option>
                                                                        @foreach ($categories as $category)
                                                                                <option value="{{ $category->id }}">
                                                                                    {{ $category->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label" name="status"
                                                                        for="inputSuccess">حالة المحل
                                                                    </label>
                                                                    <label class="checkbox-inline">
                                                                        <input type="checkbox" id="inlineCheckbox1"
                                                                            value="option1" name="status"
                                                                            id="status" />
                                                                        مخفي
                                                                    </label>
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
