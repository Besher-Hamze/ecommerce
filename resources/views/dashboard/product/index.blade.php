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
                    المنتجات </header>
                <div class="panel-body">
                    <div class="adv-table editable-table ">
                       

                        <div class="clearfix">
                            <div class="btn-group">
                                <a class="btn green" href="{{ route('product.create') }}">
                                    إضافة منتج جديد <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form action="{{route('product.index')}}" method="get">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>التصنيف</label>
                                                    <select class="js-example-basic-single" id="myOptions" name="category_id">
                                                        <option data-route="" value="0">اختر تصنيف</option>
                                                        @foreach($categories as $category)
                                                        @if($category->children->count() == 0)
                                                        <option data-route="" value="{{$category->id}}" {{ $category->id  == $category_id ? 'selected':'' }}> {{$category->name1}}</option>
                                                        @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>الشركة</label>
                                                    <select class="js-example-basic-single" style="width:100% ;" name="brand_id">
                                                        <option data-route="" value="0">اختر شركة</option>
                                                        @foreach($brands as $brand)
                                                        <option data-route="" value="{{$brand->id}}"
                                                            {{ $brand->id  == $brand_id ? 'selected':'' }}> {{$brand->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 col-sm-2 control-label">المحل
                                                </label>
                                                <div class="col-sm-4">

                                                    <select class="js-example-basic-single" id="myOptions" name="shop_id">
                                                        <option data-route="" value="0">اختر محل</option>
                                                        @foreach($shops as $shop)
                                                        <option data-route="" value="{{$shop->id}}" {{ $shop->id  == $shop_id ? 'selected':'' }}> {{$shop->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label col-lg-3"></label>
                                                <div class="col-lg-6">
                                                    <div class="">
                                                        <input type="text" class="form-control" placeholder="ابحث من خلال كلمة مفتاحية" name="search" value="{{$search}}">

                                                    </div>
                                                </div>
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-white"> بحث <i class="fa fa-search"></i></button>
                                                </span>
                                            </div>


                                        </div>
                                    </form>


                                    <section class="panel">
                                        <div class="panel-body">
                                            <section id="flip-scroll">
                                                <table class="table table-bordered table-striped table-condensed cf">
                                                    <thead class="cf">
                                                        <tr>
                                                            <th>الصورة</th>
                                                            <th> اسم المنتج</th>
                                                            <th>النوع</th>
                                                            <th>السعر</th>
                                                            <th>الكمية</th>
                                                            <th>الحالة</th>
                                                            <th>تعديل</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($products as $product)
                                                        <tr>
                                                            <td class="align-middle"><img src="{{$product->image_url}}" alt="" style="width:50px;"></td>
                                                            <td>{{ $product->name }}</td>
                                                            <td class="numeric">{{ $product->product_code }}</td>
                                                            <td class="numeric">{{ $product->price }}</td>
                                                            <td class="numeric">{{ $product->count }}</td>
                                                            <td class="numeric">{{ $product->status == '1' ? 'مخفي'
                                                                    : 'مرئي' }}</td>
                                                            <td><a class="" href="{{ route('product.update', $product->id) }}">تعديل</a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </section>
                                        </div>
                                    </section>
                                </div>
                            </div>

                        </div>
                        {{ $products->links("pagination::bootstrap-4") }}

                    </div>
                </div>
            </section>
            <!-- page end-->
        </section>
    </section>
</div>

@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $(".js-example-basic-single").select2({
            dir: "rtl"
        });
        $(".js-example-basic-multiple").select2({
            dir: "rtl"
        });
    });
</script>
@endsection