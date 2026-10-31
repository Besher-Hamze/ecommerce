@extends('dashboard.master')
@section('content')
    <div>
        <section id="main-content" dir="rtl">
            <section class="wrapper site-min-height">
                @if (session('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif
                <section class="panel">
                    <header class="panel-heading">الاعدادات</header>
                    <div class="panel-body">
                        <div class="adv-table editable-table ">
                            <div class="clearfix">
                                <div class="btn-group">
                                    <a class="btn green" href="{{ route('setting.create') }}">
                                        إضافة جديد <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="space15"></div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" id="editable-sample">
                                    <thead>
                                        <tr>
                                            <th>لوغو المتجر</th>
                                            <th>اسم المتجر</th>
                                            <th>إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                @php($logo = $settings['image'] ?? null)
                                                @if($logo)
                                                    <img src="{{ asset('img/logo/'.$logo) }}" width="60" height="40" alt="logo"
                                                         onerror="this.style.display='none'">
                                                @else
                                                    <span class="text-muted">لا يوجد</span>
                                                @endif
                                            </td>
                                            <td>{{ $settings['name'] ?? '—' }}</td>
                                            <td><a href="{{ route('setting.update') }}">تعديل</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </section>
    </div>
@endsection
