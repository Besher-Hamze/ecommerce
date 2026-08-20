@extends('dashboard.master')
@section('style')
@endsection
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
                                        <h1>الاعدادات</h1>
                                    </li>
                                    <li><a>البداية</a>
                                    </li>
                                    <li><a>تعديل </a>
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
                                            تعديل 
                                        </header>
                                        <!--tab nav start-->
                                        <section class="panel">
                                            <header class="panel-heading tab-bg-dark-navy-blue ">
                                                <ul class="nav nav-tabs">
                                                    <li class="active">
                                                        <a data-toggle="tab" href="#home">عامSEO</a>
                                                    </li>
                                                    <li class="">
                                                        <a data-toggle="tab" href="#profile">بيانات المتجر</a>
                                                    </li>
                                                    <li class="">
                                                        <a data-toggle="tab" href="#contact">لوغو المتجر</a>
                                                    </li>
                                                   
                                                </ul>
                                            </header>
                                            <form class="form-horizontal tasi-form" method="post" id="myForm"
                                                action="{{ route('setting.update_save') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="panel-body">
                                                    <div class="tab-content">
                                                        <div id="home" class="tab-pane active">                                 
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">عنوان الميتا
                                                                    تاج</label>
                                                                <div class="col-sm-10">																	                                @php($project_meta_title=\App\Models\Setting::where('key','meta_title')->first()->value)
                                                                    <input type="text" class="form-control req"
                                                                        name="meta_title" id="meta_title" value="{{$project_meta_title}}" />
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
																  @php($project_meta_description=\App\Models\Setting::where('key','meta_description')->first()->value)
                                                                <label class="col-sm-2 col-sm-2 control-label">علامات الميتا
                                                                    تاج</label>
                                                                <div class="col-sm-10">
                                                                    <textarea type="text" class="form-control" name="meta_description" id="eng_meta_description">{{$project_meta_description}}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
																@php($project_meta_keyword=\App\Models\Setting::where('key','meta_keyword')->first()->value)
                                                                <label class="col-sm-2 col-sm-2 control-label">كلمات الميتا
                                                                    تاغ</label>
                                                                <div class="col-sm-10">
                                                                    <textarea type="text" class="form-control" name="meta_keyword" id="eng_meta_keyword">{{$project_meta_keyword}}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div id="profile" class="tab-pane">                                                         @php($project_name=\App\Models\Setting::where('key','name')->first()->value)
                                                            <div class="row form-group">
                                                                <div class="col-lg-6">
                                                                    <label class="col-md-3  control-label">
اسم المتجر  </label>
                                                                    <div class="col-md-8">
                                                                        <input type="text" name="name"
                                                                            id="name" class="form-control req"
                                                                          placeholder="اسم المتجر..." value="{{$project_name}}" />
                                                                        <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label class="col-md-3  control-label">
																		@php($project_owner=\App\Models\Setting::where('key','owner')->first()->value)
                                                                      مالك المتجر
                                                                    </label>
                                                                    <div class="col-md-8">
                                                                        <input type="text" 
                                                                            name="owner" id="owner"
                                                                            class="form-control req"  placeholder="مالك المتجر..." value="{{$project_owner}}"/>
                                                                    </div>
                                                                </div>
                                                            </div>  
                                                            <div class="row form-group">
																@php($project_address=\App\Models\Setting::where('key','address')->first()->value)
                                                                <div class="col-lg-6">
                                                                    <label class="col-md-3  control-label">
                                                                        العنوان
                                                                    </label>
                                                                    <div class="col-md-8">
                                                                        <input type="text" name="address"
                                                                            id="address" class="form-control req"
                                                                           placeholder="عنوان المتجر..." value="{{$project_address}}"/>
                                                                        <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
																															@php($project_coordinates=\App\Models\Setting::where('key','coordinates')->first()->value)
                                                                    <label class="col-md-3  control-label">
احداثية المتجر                                                                      </label>
                                                                    <div class="col-md-8">
                                                                        <input type="text" 
                                                                            name="coordinates" id="coordinates"
                                                                            class="form-control"  placeholder="احداثيات المتجر..." value="{{$project_coordinates}}"/>
                                                                        <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                        
                                                            <div class="row form-group">
																																															@php($project_email=\App\Models\Setting::where('key','email')->first()->value)
                                                                <div class="col-lg-6">
                                                                    <label class="col-md-3  control-label">
                                                                        البريد الالكتروني
                                                                    </label>
                                                                    <div class="col-md-8">
                                                                        <input type="email" name="email"
                                                                            id="email" class="form-control req"
                                                                            placeholder="البريد الالكتروني..." value="{{$project_email}}" />
                                                                        <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
																	@php($project_work_time=\App\Models\Setting::where('key','work_time')->first()->value)
                                                                    <label class="col-md-3  control-label">
                                                                     أوقات الدوام    
                                                                    </label>
                                                                    <div class="col-md-8">
                                                                        <input type="text" 
                                                                            name="work_time" id="work_time"
                                                                            class="form-control req"  placeholder="أوقات دوام المتجر..." value="{{$project_work_time}}"/>
                                                                        <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                            </div> 
															 <div class="row form-group">
																 																																															@php($project_const_phone=\App\Models\Setting::where('key','const_phone')->first()->value)

                                                                <div class="col-lg-6">
                                                                    <label class="col-md-3  control-label">
                                                                        الهاتف الثابت
                                                                    </label>
                                                                    <div class="col-md-8">
                                                                        <input type="number" name="const_phone"
                                                                            id="const_phone" class="form-control num"
                                                                            placeholder="الهاتف الثابت..." value="{{$project_const_phone}}"/>
                                                                        <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
																	@php($project_wats_phone=\App\Models\Setting::where('key','wats_phone')->first()->value)
                                                                    <label class="col-md-3  control-label">
                                                                      رقم الوتس    
                                                                    </label>
                                                                    <div class="col-md-8">
                                                                        <input type="number" 
                                                                            name="wats_phone" id="wats_phone"
                                                                            class="form-control num"  placeholder="رقم الوتس..." value="{{$project_wats_phone}}"/>
                                                                        <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                            </div> 
															  <div class="col-lg-6 form-group">
																  @php($project_about=\App\Models\Setting::where('key','about')->first()->value)
                                                                    <label class="col-md-3  control-label">
                                                                        حول المتجر
                                                                    </label>
                                                                    <div class="col-md-8">                                                                                                              <textarea type="text" class="form-control" name="about" id="about">{{$project_about}}</textarea>
                                                                     <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                        <div id="contact" class="tab-pane">
															 @php($project_image=\App\Models\Setting::where('key','image')->first()->value)
                                                            <div class="form-group last">
                                                                <label class="control-label col-md-3">لوغو
                                                                    المتجر</label>
                                                                <div class="col-md-9">
                                                                    <div class="fileupload fileupload-new"
                                                                        data-provides="fileupload">
                                                                        <div class="fileupload-new thumbnail"
                                                                            style="width: 200px; height: 150px;">
                                                                            <img src="{{ asset('img/logo/'.$project_image) }}"
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
                                                                                <input type="file" class="default "
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
															   
                                                            <!--main content end-->
                                                            <div class="text-center">
                                                                <button type="submit" class="btn btn-success btn-lg "
                                                                    id="update">حفظ</button>
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
