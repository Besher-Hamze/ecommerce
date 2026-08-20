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
                                        <h1>المنتجات</h1>
                                    </li>
                                    <li><a>البداية</a>
                                    </li>
                                    <li><a>إضافة منتج</a>
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
 <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.csv" required>
                            <button type="submit">استيراد المنتجات</button>
                        </form>
                            <!-- page start-->
                            <div class="row">
                                <div class="col-lg-12">
                                    <section class="panel">
                                        <header class="panel-heading">
                                            إضافة منتج
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
                                                    <li class="">
                                                        <a data-toggle="tab" href="#contact">صور المنتج</a>
                                                    </li>
                                                </ul>
                                            </header>
                                            <form class="form-horizontal tasi-form" method="post" id="myForm"
                                                action="{{ route('product.store') }}" enctype="multipart/form-data">
                                                @csrf
                                                <div class="panel-body">
                                                    <div class="tab-content">
                                                        <div id="home" class="tab-pane active">
															<div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">نوع المنتج
                                                                </label>
                                                                <div class="col-sm-10">
                                                                   <select class="js-example-basic-single" id="myOptions" name="product_code">
																	
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">اسم المنتج
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" name="eng_name" id="eng_name"
                                                                        class="form-control req" />
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-lg-2 col-sm-2 control-label">التفاصيل
                                                                </label>
                                                                <div class="panel-body col-lg-10">
                                                                    <div class="summernote " id="eng_summernote"
                                                                        ></div>
                                                                    <textarea style="display:none;" class="req" name="eng_description" id="eng_description"></textarea>
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">عنوان الميتا
                                                                    تاج</label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" class="form-control "
                                                                        name="eng_meta_title" id="eng_meta_title" />
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">علامات الميتا
                                                                    تاج</label>
                                                                <div class="col-sm-10">
                                                                    <textarea type="text" class="form-control" name="eng_meta_description" id="eng_meta_description"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">كلمات الميتا
                                                                    تاغ</label>
                                                                <div class="col-sm-10">
                                                                    <textarea type="text" class="form-control" name="eng_meta_keyword" id="eng_meta_keyword"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="about" class="tab-pane">
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">اسم المنتج
                                                                </label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" name="name"
                                                                        class="form-control req" id="name" />
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-lg-2 col-sm-2 control-label">التفاصيل
                                                                </label>
                                                                <div class="panel-body col-lg-10">
                                                                    <div class="summernote" id="summernote" >
                                                                    </div>
                                                                    <textarea style="display:none;" class="req" name="description" id="description"></textarea>
                                                                    <p class="help-block"></p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">عنوان
                                                                    الميتا
                                                                    تاج</label>
                                                                <div class="col-sm-10">
                                                                    <input type="text" class="form-control "
                                                                        name="meta_title" id="meta_title" />
                                                                    <p class="help-block"></p>

                                                                </div>

                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">علامات
                                                                    الميتا
                                                                    تاج</label>
                                                                <div class="col-sm-10">
                                                                    <textarea type="text" class="form-control" name="meta_description" id="meta_description"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-sm-2 col-sm-2 control-label">كلمات
                                                                    الميتا
                                                                    تاغ</label>
                                                                <div class="col-sm-10">
                                                                    <textarea type="text" class="form-control" name="meta_keyword" id="meta_keyword"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="profile" class="tab-pane">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <label class="control-label" name="shipping"
                                                                        for="inputSuccess"> شحن المنتج
                                                                    </label>
                                                                    <label class="checkbox-inline">
                                                                        <input type="checkbox" id="inlineCheckbox1"
                                                                            value="option1" name="shipping"
                                                                            id="shipping" />
                                                                        نعم
                                                                    </label>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label class="control-label" name="parent_id"
                                                                        for="inputSuccess">حالة المنتج
                                                                    </label>
                                                                    <label class="checkbox-inline">
                                                                        <input type="checkbox" id="inlineCheckbox1"
                                                                            value="option1" name="status"
                                                                            id="status" />
                                                                        معطل
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row form-group">
                                                                <div class="col-lg-6">
                                                                    <label class="col-md-3  control-label">
                                                                        الكمية
                                                                    </label>
                                                                    <div class="col-md-8">
                                                                        <input type="number" name="count"
                                                                            id="count" class="form-control num"
                                                                            min="1" />
                                                                        <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                  
                                                                    <label class="control-label col-md-4" name="brand_id"
                                                                        for="inputSuccess">
                                                                        الشركة</label>
                                                                    <div class="col-md-6">
                                                                        <select class="form-control m-bot15 "
                                                                            name="brand_id" id="brand_id">
                                                                            <option  >---</option>
                                                                            @foreach ($brands as $brand)
                                                                                <option value="{{ $brand->id }}">
                                                                                    {{ $brand->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row form-group">
                                                                <div class="col-lg-6">
                                                                    <label class="control-label col-md-3"
                                                                        name="category_id" for="inputSuccess">القسم
                                                                        الأصلي</label>
                                                                    <div class="col-md-8">
                                                                        <select class="form-control m-bot15 req"
                                                                            name="category_id" id="category_id">
                                                                            <option value>اختر قسم</option>
                                                                            @foreach ($categories as $category)
																			 @if($category->children->count() == 0)
		   
                                                                                    <option value="{{ $category->id }}">
                                                                                        {{ $category->name1 }}</option>    
																			@endif
                                                                            @endforeach
                                                                        </select>                                                                                                                                   
																		<p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                               
																<div class="col-lg-6">
                                                                    <label class="control-label col-md-4" name="shop_id"
                                                                        for="inputSuccess">
                                                                        المحل</label>
                                                                    <div class="col-md-6">
                                                                        <select class="form-control m-bot15 req"
                                                                            name="shop_id" id="shop_id">
                                                                            <option value>اختر محل</option>
                                                                            @foreach($shops as $shop)
                                                                                <option value="{{ $shop->id }}">
                                                                                    {{ $shop->name }}</option>
                                                                            @endforeach
                                                                        </select>														                                                                                <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <div class="col-lg-6">
                                                                    <label class="control-label col-md-3"
                                                                        name="product_code" for="inputSuccess">
                                                                        الموديل </label>
                                                                    <div class="col-md-8 col-xs-11">
                                                                        <input
                                                                            class="form-control form-control-inline input-medium req"
                                                                            size="16" type="text" value=""
                                                                            name="product_code" id="product_code" />
                                                                        <p class="help-block"></p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label class="control-label col-md-3" name="price"
                                                                        for="inputSuccess">
                                                                        السعر </label>
                                                                    <div class="col-md-8 col-xs-11">
                                                                        <input
                                                                            class="form-control form-control-inline input-medium  num"
                                                                            size="16" type="number" value=""
                                                                            min="1" name="price"
                                                                            id="price" />
                                                                    </div>                                                                                                                                                                                                                                                                                          <p class="help-block"></p>
                                                                </div>
                                                            </div>
														
                                                        </div>
                                                        <div id="contact" class="tab-pane">
                                                            <div class="form-group last">
                                                                <label class="control-label col-md-3">صورة
                                                                    المنتج</label>
                                                                <div class="col-md-9">
                                                                    <div class="fileupload fileupload-new"
                                                                        data-provides="fileupload">
                                                                        <div class="fileupload-new thumbnail"
                                                                            style="width: 200px; height: 150px;">
                                                                            <img src="https://nour.az3093.info/uploads/product/placeholder-270x270.png"
                                                                                alt="" id="pic1"/>
                                                                        </div>
                                                                        <div class="fileupload-preview fileupload-exists thumbnail"
                                                                            style="max-width: 200px; max-height: 150px; line-height: 20px;">
                                                                        </div>
                                                                        <div>
                                                                            <span class="btn btn-white btn-file">
                                                                                <span class="fileupload-new"><i
                                                                                        class="fa fa-paper-clip"></i>
                                                                                    Select image</span>
                                                                                <span class="fileupload-exists"><i
                                                                                        class="fa fa-undo"></i>
                                                                                    Change</span>
                                                                                <input type="file" class="default "
                                                                                    name="image" id="image" />
                                                                            </span>
                                                                            <a href="#"
                                                                                class="btn btn-danger fileupload-exists"
                                                                                data-dismiss="fileupload"><i
                                                                                    class="fa fa-trash"></i> Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="row form-group">
                                                                <div class=" col-md-6 ">

                                                                    <button id="addImage" class="btn btn-primary"
                                                                        type="button">إضافة صورة +</button>

                                                                </div>

                                                            </div>
                                                            <div class="row " id="new">

                                                            </div>

                                                            <!--main content end-->
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

    <div id="imagediv" hidden>
        <div class=" col-md-6 ">

            <label for="img" class="col-lg-3 col-sm-2 control-label">إضافة صورة </label>
            <div class="col-lg-9">
                <input type="file" name="extra_image[]" class=" img" required>
                <span class="help-block">
                </span>
                <button type="button" style="margin-top: 6px" class="btn-link delete">حذف</button>
            </div>
            <div class="col-md-12">
                <img class="blah" src="" style="max-width: 180px; margin-top: 15px" alt="" />
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('my_js\submit.js') }}"></script>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#img-upload-tag').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $('div').on("click", 'button.delete', function() {
            var d = $(this).parent().parent();
            //console.log(d);
            d.remove();
        });
        $(document).on("change", '.img', function() {
            $(this).closest("div").find('.help-block').html('');

            var imagetag = $(this).closest("div").next('div').find('.blah');
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    imagetag.attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
            $('div').removeClass('has-error');
            $(this).closest("div").removeClass('has-error');

            var size = Math.round(this.files[0].size / 1024);
            if (size > 2048) {
                $(this).closest("div").find('.help-block').html(
                '<strong>حجم الصورة يجب ألا يتجاوز 2048KB</strong>');
                $(this).closest("div").addClass('has-error');
                $(this).focus();
                $(this).val('');

            }
        });
        $('#addImage').click(function() {
            $('#new').append($('#imagediv').clone().html());
        })
	
    </script>
   <script type="text/javascript">
      $(document).ready(function () {
          $(".js-example-basic-single").select2({
              dir: "rtl"
          });
          $(".js-example-basic-multiple").select2({
              dir: "rtl"
          });
      });
    </script>
  <script type="text/javascript">

    $('#myOptions').change(function() {
    
  
	  var urlajax=$("#myOptions option:selected").data('route');
          $.ajax({
                url: urlajax, // router
                method: "get",
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,

                success: function (res) {
					
                      document.getElementById("name").value=res['product']['name'];
                      document.getElementById("eng_name").value=res['product']['eng_name'];
					  document.getElementById("eng_meta_keyword").value=res['product']['eng_meta_keyword'];
					  document.getElementById("eng_meta_title").value=res['product']['eng_meta_title'];
                      document.getElementById("eng_meta_description").value=res['product']['eng_meta_description'];
					  document.getElementById("meta_keyword").value=res['product']['meta_keyword'];
					  document.getElementById("meta_title").value=res['product']['meta_title'];
                      document.getElementById("meta_description").value=res['product']['meta_description'];
					  document.getElementById("product_code").value=res['product']['product_code'];
                     $("#summernote").code(res['product']['description']);
					 $("#eng_summernote").code(res['product']['eng_description']);
                     $("#name").prop('readonly', true);
					 $("#name").prop('readonly', true);
					 $("#eng_name").prop('readonly', true);
					 $("#eng_meta_keyword").prop('readonly', true);
					 $("#eng_meta_description").prop('readonly', true);
					 $("#meta_keyword").prop('readonly', true);
					 $("#meta_title").prop('readonly', true);
				     $("#meta_description").prop('readonly', true);
					 $("#eng_meta_title").prop('readonly', true);
					 $("#product_code").prop('readonly', true);
					 document.getElementById("image").disabled = true;
					 var path='https://nour.az3093.info/uploads/product/'+ res["product"]["image_url"];
					 $("#pic1").attr("src",path);
					console.log($("#pic1").attr("src"));
                },
            });  
	});
    </script>

@endsection
