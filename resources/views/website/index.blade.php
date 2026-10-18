@extends('website.master')
@section('title', ($storeSettings['meta_title'] ?? 'Noor-Store') . ' | Home')
@section('content')

<div class="container-fluid py-5 mb-4 hero-header">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-md-12 col-lg-7">
                <p class="mb-2 text-secondary fw-semibold">Makeup · Smart recommendations</p>
                <h1 class="mb-3 display-3 text-primary">{{ $storeSettings['name'] ?? 'Noor-Store' }}</h1>
                <p class="mb-4 fs-5" style="max-width:28rem;color:var(--noor-muted)">
                    {{ $storeSettings['about'] ?? 'Authentic makeup with recommendations that build your routine.' }}
                </p>
                <form action="{{ route('products.all') }}" method="GET">
                    <div class="position-relative mx-auto">
                        <input name="search" class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill"
                               type="text" placeholder="Search lipstick, mascara, brand…" required>
                        <button type="submit"
                                class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100"
                                style="top: 0; right: 25%;">Shop now</button>
                    </div>
                </form>
            </div>
            <div class="col-md-12 col-lg-5">
                <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                    <div class="carousel-inner" role="listbox">
                        @forelse($slider_images as $index => $image)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }} rounded">
                                <img src="{{ asset('uploads/slider/'.$image->image) }}"
                                     class="img-fluid w-100 h-100 bg-secondary rounded" alt="{{ $image->text_en }}">
                            </div>
                        @empty
                            <div class="carousel-item active rounded bg-secondary p-5 text-center">
                                <h3 class="text-primary py-5">Build your beauty routine</h3>
                            </div>
                        @endforelse
                    </div>
                    @if($slider_images->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid featurs py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4 h-100">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-3 mx-auto">
                        <i class="fas fa-car-side fa-2x text-white"></i>
                    </div>
                    <h5>Free shipping</h5>
                    <p class="mb-0 small">On orders over ${{ $storeSettings['free_shipping_threshold'] ?? 50 }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4 h-100">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-3 mx-auto">
                        <i class="fas fa-magic fa-2x text-white"></i>
                    </div>
                    <h5>Smart picks</h5>
                    <p class="mb-0 small">Apriori · Upsell · Collaborative filtering</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4 h-100">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-3 mx-auto">
                        <i class="fas fa-certificate fa-2x text-white"></i>
                    </div>
                    <h5>Authentic makeup</h5>
                    <p class="mb-0 small">{{ count($taxonomy ?? []) }} categories · trusted brands</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4 h-100">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-3 mx-auto">
                        <i class="fa fa-phone-alt fa-2x text-white"></i>
                    </div>
                    <h5>Support</h5>
                    <p class="mb-0 small">{{ $storeSettings['work_time'] ?? 'Sat - Thu' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if($forYou->isNotEmpty())
<section class="container py-5">
    <div class="mb-4">
        <h2 class="noor-section-title display-6 mb-1">Picked for you</h2>
        <p class="noor-section-sub mb-0">Personalised with item-based collaborative filtering from your browsing and purchases.</p>
    </div>
    <div class="row g-4">
        @foreach($forYou as $product)
            @include('website.product-card', ['product' => $product, 'recSource' => 'cf'])
        @endforeach
    </div>
</section>
@endif

@if($trending->isNotEmpty())
<section class="container py-5">
    <div class="mb-4">
        <h2 class="noor-section-title display-6 mb-1">Trending now</h2>
        <p class="noor-section-sub mb-0">What shoppers are adding this month.</p>
    </div>
    <div class="row g-4">
        @foreach($trending as $product)
            @include('website.product-card', ['product' => $product, 'recSource' => 'trending'])
        @endforeach
    </div>
</section>
@endif

<section class="container-fluid py-5" style="background:var(--noor-cream)">
    <div class="container">
        <div class="mb-4 d-flex justify-content-between align-items-end flex-wrap gap-2">
            <div>
                <h2 class="noor-section-title display-6 mb-1">Shop by look</h2>
                <p class="noor-section-sub mb-0">Lips, eyes, face & nails.</p>
            </div>
            <a href="{{ route('products.all') }}" class="btn border border-secondary rounded-pill text-primary">View all</a>
        </div>
        <div class="row g-3">
            @foreach($taxonomy ?? [] as $slug => $meta)
                <div class="col-6 col-md-3">
                    <a href="{{ route('products.all', ['category_id' => optional(($navCategories ?? collect())->firstWhere('slug', $slug))->id]) }}"
                       class="d-block text-decoration-none p-4 rounded-3 h-100"
                       style="background:#fff;border:1px solid rgba(212,106,146,.2)">
                        <i class="fas {{ $meta[2] }} fa-2x text-primary mb-2"></i>
                        <h5 class="mb-1 text-dark">{{ $meta[1] }}</h5>
                        <p class="small text-muted mb-0">{{ $meta[0] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if($bestSellers->isNotEmpty())
<section class="container py-5">
    <div class="mb-4">
        <h2 class="noor-section-title display-6 mb-1">Best sellers</h2>
        <p class="noor-section-sub mb-0">Most loved across Noor-Store.</p>
    </div>
    <div class="row g-4">
        @foreach($bestSellers as $product)
            @include('website.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

@if($newArrivals->isNotEmpty())
<section class="container py-5">
    <div class="mb-4">
        <h2 class="noor-section-title display-6 mb-1">New arrivals</h2>
        <p class="noor-section-sub mb-0">Fresh shades and formulas.</p>
    </div>
    <div class="row g-4">
        @foreach($newArrivals as $product)
            @include('website.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

@if($reviews->isNotEmpty())
<section class="container py-5">
    <div class="text-center mb-4">
        <h2 class="noor-section-title display-6">What customers say</h2>
    </div>
    <div class="row g-4">
        @foreach($reviews->take(3) as $review)
            <div class="col-md-4">
                <div class="p-4 h-100 rounded-3 bg-light">
                    @include('website.partials.stars', ['rating' => $review->star, 'count' => 0])
                    <p class="mt-3 mb-3">“{{ \Illuminate\Support\Str::limit($review->review, 140) }}”</p>
                    <strong>{{ $review->user->name ?? 'Customer' }}</strong>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@endsection
