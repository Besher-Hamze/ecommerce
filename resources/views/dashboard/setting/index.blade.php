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
                        الاعدادات </header>
                    <div class="panel-body">
                        <div class="adv-table editable-table ">
                            <div class="clearfix">
                                <div class="btn-group">
                                    <a class="btn green" href="{{ route('setting.create') }}">
                                        إضافة  جديد <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="space15"></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" id="editable-sample">
                                    <thead>
                                        <tr>
                                            <th>لوغو المتجر</th>
                                            <th> اسم المتجر</th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <tr class="">                                                                                                                                      <td>@php($project_image=\App\Models\Setting::where('key','image')->first()->value)  <img src="{{ asset('img/logo/'.$project_image) }}" width="60px"
                                                    height="40px" /></td>
                                                <td>@php($project_name=\App\Models\Setting::where('key','name')->first()->value){{$project_name}}</td>
                                                <td><a class="" href="{{ route('setting.update') }}">تعديل</a></td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- page end-->
            </section>
        </section>
    </div>
@endsection
