@extends('website.master')
@section('title', 'Wishlist | Noor-Store')
@section('content')
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">My Wishlist</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('welcome.index') }}">Home</a></li>
        <li class="breadcrumb-item active text-white">Wishlist</li>
    </ol>
</div>

<div class="container py-5">
    @if($products->isEmpty())
        <div class="text-center py-5">
            <i class="far fa-heart fa-3x text-muted mb-3"></i>
            <h3 class="mb-2">Your wishlist is empty</h3>
            <p class="text-muted mb-4">Save products you love and find them here later.</p>
            <a href="{{ route('products.all') }}" class="btn btn-primary rounded-pill px-4">Browse shop</a>
        </div>
    @else
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">{{ $products->count() }} saved item{{ $products->count() === 1 ? '' : 's' }}</h2>
        </div>
        <div class="row g-4">
            @foreach($products as $product)
                @php
                    $img = \App\Models\Product::imageUrl($product->image_url);
                @endphp
                <div class="col-6 col-md-4 col-lg-3" id="wish-item-{{ $product->shop_product_id }}">
                    <div class="noor-product-card w-100 d-flex flex-column position-relative">
                        <button type="button"
                                class="btn btn-sm btn-light rounded-circle position-absolute wishlist-toggle active"
                                style="top:10px;right:10px;z-index:2"
                                data-id="{{ $product->shop_product_id }}"
                                data-route="{{ route('favorite.add', $product->shop_product_id) }}"
                                title="Remove from wishlist"
                                aria-label="Remove from wishlist">
                            <i class="fas fa-heart text-danger"></i>
                        </button>
                        <a href="{{ route('product.details', $product->product_id) }}" class="thumb">
                            <img src="{{ $img }}"
                                 alt="{{ $product->eng_name ?? $product->name }}"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';">
                        </a>
                        <div class="p-3 d-flex flex-column flex-grow-1">
                            @if($product->brand_name)
                                <div class="brand mb-1">{{ $product->brand_name }}</div>
                            @endif
                            <a href="{{ route('product.details', $product->product_id) }}" class="text-decoration-none">
                                <div class="title mb-2">{{ $product->eng_name ?? $product->name }}</div>
                            </a>
                            @include('website.partials.stars', ['rating' => $product->rating_avg ?? 0, 'count' => $product->rating_count ?? 0])
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 gap-2">
                                <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                                <button type="button"
                                        class="btn btn-sm border border-secondary rounded-pill text-primary add-to-cart"
                                        data-id="{{ $product->shop_product_id }}"
                                        data-route="{{ route('cart.store', $product->shop_product_id) }}">
                                    <i class="fa fa-shopping-bag"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
