@extends('website.master')
@section('content')
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Products</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Pages</a></li>
        <li class="breadcrumb-item active text-white">products</li>
    </ol>
</div>
<div class="container-fluid service py-5">
    <div class="container py-5">
        <div class="row g-4 justify-content-center">
<div class="tab-content">
                {{-- All Products --}}
                <div id="tab-all" class="tab-pane fade show p-0 active">
                    <div class="row g-4">
                        @foreach($products as $product)
                        @include('website.product-card', ['product' => $product])
                        @endforeach
                    </div>
                    <div class="pagination-wrapper">
    {{$products->links('pagination::bootstrap-4') }}
</div>


                </div>
               
</div>
</div>

</div></div>











@endsection