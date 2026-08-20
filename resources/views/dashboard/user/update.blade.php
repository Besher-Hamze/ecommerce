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
                                        <h1>المستخدمين</h1>
                                    </li>
                                    <li><a>البداية</a>
                                    </li>
                                    <li><a>إضافة مستخدم</a>
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
                            <section class="panel">
                                <header class="panel-heading">
                                    إضافة مستخدم
                                </header>
                                <!--tab nav start-->
                                <section class="panel">
                                    <form class="form-horizontal tasi-form" method="post" id="myForm"
                                        action="{{ route('user.update_save',$user->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="panel-body">
                                            <div class="tab-content">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <section class="panel">
                                                            <div class="panel-body">
                                                                <div class="form">
                                                                    <div class="form-group ">
                                                                        <label for="username"
                                                                            class="control-label col-lg-2 ">اسم
                                                                            المستخدم</label>
                                                                        <div class="col-lg-10">
                                                                            <input class="form-control req" id="name"
                                                                                name="name" type="text"
                                                                                value="{{ $user->name }}" />
                                                                            <p class="help-block"></p>
                                                                            <span id="name_error"></span>
                                                                        </div>

                                                                    </div>
                                                                    <div class="form-group ">
                                                                        <label for="password"
                                                                            class="control-label col-lg-2 ">كلمة
                                                                            السر</label>
                                                                        <div class="col-lg-10">
                                                                            <input class="form-control req" id="password"
                                                                                name="password" type="password" />
                                                                            <p class="help-block"></p>
                                                                            <span id="password_error"></span>
                                                                        </div>

                                                                    </div>
                                                                    <div class="form-group ">
                                                                        <label for="password_confirmation"
                                                                            class="control-label col-lg-2 ">تأكيد
                                                                            كلمة السر</label>
                                                                        <div class="col-lg-10">
                                                                            <input class="form-control req"
                                                                                id="password_confirmation"
                                                                                name="password_confirmation"
                                                                                type="password" />
                                                                            <p class="help-block"></p>
                                                                            <span id="password_confirmation_error"></span>
                                                                        </div>

                                                                    </div>
                                                                    <div class="form-group ">
                                                                        <label for="email"
                                                                            class="control-label col-lg-2 ">الايميل</label>
                                                                        <div class="col-lg-10">
                                                                            <input class="form-control req" id="email"
                                                                                name="email" type="email"
                                                                                value="{{ $user->email }}" readonly />
                                                                            <p class="help-block"></p>
                                                                            <span id="email_error"></span>
                                                                        </div>

                                                                    </div>
                                                                    <div class="form-group ">
                                                                        <label for="email"
                                                                            class="control-label col-lg-2">الدور</label>
                                                                        <div class="col-lg-10">
                                                                            <select class="form-control m-bot15 col-lg-6"
                                                                                name="role" id="role">
                                                                                <option disabled selected>----</option>
                                                                                <option value="0"
                                                                                    {{ $user->role == '0' ? 'selected' : '' }}>
                                                                                    مستخدم</option>
                                                                                <option value="1" {{ $user->role == '1' ? 'selected' : '' }} >
                                                                                    مدير</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    {{-- <div class="form-group ">
                                                                            <label for="agree"
                                                                                class="control-label col-lg-2 col-sm-3">Agree
                                                                                to Our Policy</label>
                                                                            <div class="col-lg-10 col-sm-9">
                                                                                <input type="checkbox" style="width: 20px"
                                                                                    class="checkbox form-control"
                                                                                    id="agree" name="agree" />
                                                                            </div>
                                                                        </div> --}}
                                                                    {{-- <div class="form-group ">
                                                                            <label for="newsletter"
                                                                                class="control-label col-lg-2 col-sm-3">Receive
                                                                                the Newsletter</label>
                                                                            <div class="col-lg-10 col-sm-9">
                                                                                <input type="checkbox" style="width: 20px"
                                                                                    class="checkbox form-control"
                                                                                    id="newsletter" name="newsletter" />
                                                                            </div>
                                                                        </div> --}}
                                                                </div>
                                                            </div>
                                                        </section>
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-success btn-lg "
                                                        id="update">حفظ</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </section>
                            </section>
                    </div>
                </div>
            </section>
        </section>
    </section>
    <!-- page end-->
    </section>

    <!--main content end-->
@endsection
@section('script')
    <script src="{{ asset('my_js\brand_submit.js') }} "></script>
@endsection
