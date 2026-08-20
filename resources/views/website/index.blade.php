@extends('website.master')
@section('content')

<!-- Hero Start -->
<div class="container-fluid py-5 mb-5 hero-header">
    <div class="container py-5">
        <div class="row g-5 align-items-center">
            <div class="col-md-12 col-lg-7">
                <h4 class="mb-3 text-secondary">Premium Makeup Products</h4>
                <h1 class="mb-5 display-3 text-primary">Luxury Makeup for Every Look</h1>

                <form action="{{ route('products.all') }}" method="GET">
                    <div class="position-relative mx-auto">
                        <input
                            name="search"
                            class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill"
                            type="text"
                            placeholder="Search"
                            required>
                        <button
                            type="submit"
                            class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100"
                            style="top: 0; right: 25%;">
                            Submit Now
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-12 col-lg-5">
                <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                    <div class="carousel-inner" role="listbox">
                        @foreach($slider_images as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }} rounded">
                            <img src="{{ asset('uploads/slider/'.$image->image) }}" class="img-fluid w-100 h-100 bg-secondary rounded" alt="Slide {{ $index + 1 }}">
                        </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Hero End -->


<!-- Featurs Section Start -->
<div class="container-fluid featurs py-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-car-side fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>Free Shipping</h5>
                        <p class="mb-0">Free on order over $300</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-user-shield fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>Security Payment</h5>
                        <p class="mb-0">100% security payment</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fas fa-exchange-alt fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>30 Day Return</h5>
                        <p class="mb-0">30 day money guarantee</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="featurs-item text-center rounded bg-light p-4">
                    <div class="featurs-icon btn-square rounded-circle bg-secondary mb-5 mx-auto">
                        <i class="fa fa-phone-alt fa-3x text-white"></i>
                    </div>
                    <div class="featurs-content text-center">
                        <h5>24/7 Support</h5>
                        <p class="mb-0">Support every time fast</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Featurs Section End -->


<!-- Fruits Shop Start-->
<div class="container-fluid fruite py-5">
    <div class="container py-5">
        <div class="tab-class text-center">
            <div class="row g-4">
                <div class="col-lg-4 text-start">
                    <h1>Our Organic Products</h1>
                </div>
                <div class="col-lg-8 text-end">
                    <ul class="nav nav-pills d-inline-flex text-center mb-5">
                        <li class="nav-item">
                            <a class="d-flex m-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill" href="#tab-1">
                                <span class="text-dark" style="width: 130px;">All Products</span>
                            </a>
                        </li>
                        @foreach($categories->take(4) as $category)
                        <li class="nav-item">
                            <a class="d-flex py-2 m-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-{{ $category->id }}">
                                <span class="text-dark" style="width: 130px;">{{$category->eng_name}}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                {{-- All Products --}}
                <div id="tab-all" class="tab-pane fade show p-0 active">
                    <div class="row g-4">
                        @foreach($allProducts as $product)
                        @include('website.product-card', ['product' => $product])
                        @endforeach
                    </div>
                </div>

                {{-- كل صنف --}}
                @foreach($categories as $category)
                <div id="tab-{{ $category->id }}" class="tab-pane fade show p-0">
                    <div class="row g-4">
                        @foreach($category->products->take(5) as $product)
                        @include('website.product-card', ['product' => $product])
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
<!-- Fruits Shop End-->


<!-- Features Start -->
<div class="container-fluid service py-5">
    <div class="container py-5">
        <div class="row g-4 justify-content-center">

            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-4">
                <a href="#">
                    <div class="service-item bg-secondary rounded border border-secondary">
                        <div class="px-4 rounded-bottom">
                            <div class="service-content bg-primary text-center p-4 rounded">
                                <h5 class="text-white">100% Authentic Makeup</h5>
                                <h3 class="mb-0">20% OFF</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-4">
                <a href="#">
                    <div class="service-item bg-dark rounded border border-dark">
                        <div class="px-4 rounded-bottom">
                            <div class="service-content bg-light text-center p-4 rounded">
                                <h5 class="text-primary">Premium Skincare</h5>
                                <h3 class="mb-0">Free Delivery</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-4">
                <a href="#">
                    <div class="service-item bg-primary rounded border border-primary">
                        <div class="px-4 rounded-bottom">
                            <div class="service-content bg-secondary text-center p-4 rounded">
                                <h5 class="text-white">Exclusive Makeup Bundles</h5>
                                <h3 class="mb-0">Up to 30% OFF</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>
<!-- Features End -->


@if(!empty($bestSellingProducts) && count($bestSellingProducts) > 0)
<!-- Vesitable Shop Start-->
<div class="container-fluid vesitable py-5 bg-secondary">
    <div class="container py-5">
        <h1 class="mb-0">Best Selling Makeup Products</h1>
        <div class="owl-carousel vegetable-carousel justify-content-center">
            @foreach($bestSellingProducts as $product)
            <div class="item">
                <div class="border border-primary rounded position-relative vesitable-item h-100 d-flex flex-column bg-white">

                    <div class="p-4 pb-0 rounded-bottom d-flex flex-column h-100">

                        <h4 class="product-title">
                            <a href="{{ route('product.details', $product->id) }}"
                                class="text-dark text-decoration-none">
                                {{ $product->name }}
                            </a>
                        </h4>

                        <p class="product-desc">
                            {{ \Illuminate\Support\Str::limit($product->description, 120) }}
                        </p>

                        <div class="d-flex justify-content-between flex-lg-wrap mt-auto">
                            <p class="text-dark fs-5 fw-bold mb-0">
                                ${{ $product->price }}
                            </p>

                            <a href="{{ route('product.details', $product->id) }}"
                                class="btn border border-secondary rounded-pill px-3 py-1 mb-4 text-primary">
                                <i class="fa fa-shopping-bag me-2 text-primary"></i>
                                View
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</div>
@endif
<!-- Vesitable Shop End -->





<!-- Bestsaler Product Start -->
<!-- <div class="container-fluid banner bg-secondary my-5">
    <div class="container py-5">
        <div class="text-center mx-auto mb-5" style="max-width: 700px;">
            <h1 class="display-4">Bestseller Products</h1>
            <p>Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-6 col-xl-4">
                <div class="p-4 rounded bg-light">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <img src="img/best-product-1.jpg" class="img-fluid rounded-circle w-100" alt="">
                        </div>
                        <div class="col-6">
                            <a href="#" class="h5">Organic Tomato</a>
                            <div class="d-flex my-3">
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star text-primary"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <h4 class="mb-3">3.12 $</h4>
                            <a href="#" class="btn border border-secondary rounded-pill px-3 text-primary"><i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div> -->
<!-- Bestsaler Product End -->


<!-- Fact Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="bg-light p-5 rounded">
            <div class="row g-4 justify-content-center">
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="counter bg-white rounded p-5">
                        <i class="fa fa-users text-secondary"></i>
                        <h4>satisfied customers</h4>
                        <h1>1963</h1>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="counter bg-white rounded p-5">
                        <i class="fa fa-users text-secondary"></i>
                        <h4>quality of service</h4>
                        <h1>99%</h1>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="counter bg-white rounded p-5">
                        <i class="fa fa-users text-secondary"></i>
                        <h4>quality certificates</h4>
                        <h1>33</h1>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="counter bg-white rounded p-5">
                        <i class="fa fa-users text-secondary"></i>
                        <h4>Available Products</h4>
                        <h1>789</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fact Start -->


<!-- Tastimonial Start -->
@if(!empty($reviews) && count($reviews) > 0)
<div class="container-fluid testimonial py-5">
    <div class="container py-5">
        <div class="testimonial-header text-center">
            <h4 class="text-primary">Our Testimonial</h4>
            <h1 class="display-5 mb-5 text-dark">Our Client Saying!</h1>
        </div>
        <div class="owl-carousel testimonial-carousel">
            @foreach($reviews as $review)
            <div class="testimonial-item img-border-radius bg-light rounded p-4">
                <div class="position-relative">
                    <i class="fa fa-quote-right fa-2x text-secondary position-absolute" style="bottom: 30px; right: 0;"></i>
                    <div class="mb-4 pb-4 border-bottom border-secondary">
                        <p class="mb-0">{{ $review->review }}</p>
                    </div>
                    <div class="d-flex align-items-center flex-nowrap">
                        <div class="bg-secondary rounded">
                            <img src="" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="{{ $review->user->name }}">
                        </div>
                        <div class="ms-4 d-block">
                            <h4 class="text-dark">{{ $review->user->name }}</h4>
                            <p class="m-0 pb-3">{{ $review->user->profession }}</p>
                            <div class="d-flex pe-5">
                                @for($i = 0; $i < $review->star; $i++)
                                    <i class="fas fa-star text-primary"></i>
                                    @endfor
                                    @for($i = $review->star; $i < 5; $i++)
                                        <i class="fas fa-star"></i>
                                        @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>
@endif
<!-- Tastimonial End -->




@endsection