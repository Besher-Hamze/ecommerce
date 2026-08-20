@extends('website.master')
@section('content')
<div class="container">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
</div>
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Cart</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Pages</a></li>
        <li class="breadcrumb-item active text-white">Cart</li>
    </ol>
</div>
<!-- Single Page Header End -->
<!-- Cart Page Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
     
        <div class="row g-4 ">
            <div class="col-12"></div>
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div class="bg-light rounded">
                    <div class="p-4">
                        <h1 class="display-6 mb-4">User <span class="fw-normal">Profile</span></h1>
                        <div class="d-flex  mb-4">
                            <h5 class="mb-0 me-4">Username :</h5>
                            <div class="">
                                <p class="mb-0">{{Auth::user()->name}}</p>
                            </div>
                        </div>
                        <div class="d-flex ">
                            <h5 class="mb-0 me-4">Email :</h5>
                            <div class="">
                                <p class="mb-0">{{Auth::user()->email}}</p>
                            </div>
                          
                        </div>
                    </div>
                   
            </div>
        </div>
    </div>
</div>
<!-- Cart Page End -->
@endsection