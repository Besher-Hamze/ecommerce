@extends('website.master')
@section('content')
<style>
    .colors {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .color-swatch {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 1px solid #ccc;
        cursor: pointer;
    }

    .color-swatch:hover {
        transform: scale(1.1);
        border: 2px solid #000;
    }

    .stars {
        display: flex;
        gap: 4px;
    }

    .star {
        font-size: 30px;
        cursor: pointer;
        color: lightgray;
        transition: .2s;
    }

    .star.filled {
        color: gold;
    }

    .product-image-wrapper {

        background: #fff;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        /* ← يجعل الصورة أعلى */
        padding-bottom: 40px;
        /* مسافة خفيفة من الأعلى */
    }
</style>
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Shop Detail</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Pages</a></li>
        <li class="breadcrumb-item active text-white">Shop Detail</li>
    </ol>
</div>
<!-- Single Page Header End -->
<!-- Single Product Start -->
<div class="container-fluid py-5 mt-5">
    <div class="container py-5">
        <div class="row g-4 mb-5">
            <div class="col-lg-8 col-xl-9">
                <div class="row g-4">
                    <div class="col-lg-4 ">
                        <div class="d-flex justify-content-center align-items-center product-image-wrapper">
                            <img src="{{$product->image_url}}"
                                alt="{{ $product->name }}"
                                class="img-fluid product-image"
                                onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';">
                        </div>
                    </div>
                    <div class=" col-lg-8">
                        <h4 class="fw-bold mb-3">{{$product->name}}</h4>
                        <p class="mb-3">Category: {{$product->category_name}}</p>
                        @if($product->price > 0)
                        <h5 class="fw-bold mb-3">{{$product->price}} $</h5>
                        @endif
                        <div class="d-flex mb-4">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <=$rating)
                                <i class="fa fa-star text-secondary"></i>
                                @else
                                <i class="fa fa-star"></i>
                                @endif
                                @endfor
                        </div>

                        <div class="colors">

                            @php
                            $colors = json_decode($product->product_colors ?? '[]', true);
                            @endphp

                            @if(!empty($colors) && count($colors) > 0)
                            @foreach($colors as $color)
                            <div class="color-swatch"
                                style="background: {{ $color['hex_value'] }}"
                                title="{{ $color['colour_name'] }}">
                            </div>
                            @endforeach
                            @endif
                        </div>
                        <div class="d-flex align-items-center mb-4 pt-2">
                            @if($product->price > 0)
                            <strong class="text-dark mr-3">{{__('product_details.qty')}}:</strong>

                            <div class="input-group quantity mr-3" style="width: 130px;">
                                <form method="post">
                                    <input type="text" name="qty" id="qty"
                                        class="form-control bg-secondary border-0 text-center"
                                        value="1"
                                        {{ $product->price <= 0 ? 'disabled' : '' }}>

                                    <input name="id" id="id" type="hidden" value="{{$product->id}}">
                                    <input name="route" id="route" type="hidden"
                                        value="{{ route('cart.store', $product->id) }}">
                                </form>
                            </div>


                            <button class="btn border border-secondary rounded-pill px-4 py-2 text-primary"
                                type="button" id="btn">
                                <i class="fa fa-shopping-cart mr-1"></i> Add to cart
                            </button>
                            @else
                            <button class="btn border border-secondary rounded-pill px-4 py-2 text-muted"
                                type="button" disabled style="opacity:0.6; cursor:not-allowed;">
                                <i class="fa fa-times mr-1"></i> Out of stock
                            </button>
                            @endif
                        </div>

                    </div>
                    <div class="col-lg-12">
                        <nav>
                            <div class="nav nav-tabs mb-3">
                                <button class="nav-link active border-white border-bottom-0" type="button" role="tab"
                                    id="nav-about-tab" data-bs-toggle="tab" data-bs-target="#nav-about"
                                    aria-controls="nav-about" aria-selected="true">Description</button>
                                <button class="nav-link border-white border-bottom-0" type="button" role="tab"
                                    id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                    aria-controls="nav-mission" aria-selected="false">Reviews</button>
                            </div>
                        </nav>
                        <div class="tab-content mb-5">
                            <div class="tab-pane active" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab">
                                <p>{{$product->description}}.</p>
                                <!-- <div class="px-2">
                                    <div class="row g-4">
                                        <div class="col-6">
                                            <div class="row bg-light align-items-center text-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Weight</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">1 kg</p>
                                                </div>
                                            </div>
                                            <div class="row text-center align-items-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Country of Origin</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">Agro Farm</p>
                                                </div>
                                            </div>
                                            <div class="row bg-light text-center align-items-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Quality</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">Organic</p>
                                                </div>
                                            </div>
                                            <div class="row text-center align-items-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Сheck</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">Healthy</p>
                                                </div>
                                            </div>
                                            <div class="row bg-light text-center align-items-center justify-content-center py-2">
                                                <div class="col-6">
                                                    <p class="mb-0">Min Weight</p>
                                                </div>
                                                <div class="col-6">
                                                    <p class="mb-0">250 Kg</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                            <div class="tab-pane" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
                                @foreach($product->reviews as $review)
                                <div class="d-flex mb-4">
                                    <img src="{{ asset('img/avatar.jpg') }}" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                    <div>
                                        <p class="mb-2" style="font-size: 14px;">
                                            {{ $review->created_at->format('F d, Y') }}
                                        </p>

                                        <div class="d-flex justify-content-between">
                                            <h5>{{ $review->user->name }}</h5>

                                            <div class="d-flex mb-3">
                                                @if(is_numeric($review->star) && $review->star > 0)
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa fa-star {{ $i <= $review->star ? 'text-secondary' : ' ' }}"></i>
                                                    @endfor
                                                    @else
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fa fa-star"></i>
                                                        @endfor
                                                        @endif
                                            </div>
                                        </div>

                                        <p>{{ $review->review }}</p>
                                    </div>
                                </div>
                                @endforeach




                            </div>
                            <div class="tab-pane" id="nav-vision" role="tabpanel">
                                <p class="text-dark">Tempor erat elitr rebum at clita. Diam dolor diam ipsum et tempor sit. Aliqu diam
                                    amet diam et eos labore. 3</p>
                                <p class="mb-0">Diam dolor diam ipsum et tempor sit. Aliqu diam amet diam et eos labore.
                                    Clita erat ipsum et lorem et sit</p>
                            </div>
                        </div>
                    </div>
                    @auth
                    <form action="{{route('review.store')}}" method="POST">
                        @csrf
                        <h4 class="mb-5 fw-bold">Leave a Reply</h4>

                        <div class="row g-4">

                            <div class="col-lg-12">
                                <div class="border-bottom rounded my-4">
                                    <textarea name="review" class="form-control border-0"
                                        cols="30" rows="8"
                                        placeholder="Your Review *"
                                        required></textarea>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="d-flex justify-content-between py-3 mb-5">
                                    <div class="d-flex align-items-center">
                                        <p class="mb-0 me-3">Please rate:</p>

                                        <div class="stars">
                                            <span class="star" data-value="1">&#9733;</span>
                                            <span class="star" data-value="2">&#9733;</span>
                                            <span class="star" data-value="3">&#9733;</span>
                                            <span class="star" data-value="4">&#9733;</span>
                                            <span class="star" data-value="5">&#9733;</span>
                                        </div>

                                        <input type="hidden" name="rating" id="rating" required>
                                    </div>

                                    <button type="submit"
                                        class="btn border border-secondary text-primary rounded-pill px-4 py-3">
                                        Post Comment
                                    </button>
                                </div>
                            </div>

                        </div>

                        <input type="hidden" name="product_id" value="{{$product->id}}">
                    </form>
                    @endauth
                </div>
            </div>

        </div>
        <h1 class="fw-bold mb-0">Related products</h1>
        <div class="vesitable">
            <div class="owl-carousel vegetable-carousel justify-content-center">

                @foreach($relatedProducts as $item)
                <div class="item">
                    <div class="border border-primary rounded position-relative vesitable-item h-100 d-flex flex-column">

                        <div class="p-4 pb-0 rounded-bottom d-flex flex-column h-100">

                            <h4 class="product-title">
                                <a href="{{ route('product.details', $item->id) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $item->name }}
                                </a>
                            </h4>

                            <p class="product-desc">
                                {{ \Illuminate\Support\Str::limit($item->description, 120) }}
                            </p>

                            <div class="d-flex justify-content-between flex-lg-wrap mt-auto">
                                <p class="text-dark fs-5 fw-bold mb-0">
                                    ${{ $item->price }}
                                </p>

                                <a href="{{ route('product.details', $item->id) }}"
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
</div>
<!-- Single Product End -->
<script>
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');

    function paint(rating) {
        stars.forEach(s => {
            const v = parseInt(s.dataset.value, 10);
            s.classList.toggle('filled', v <= rating);
        });
    }

    stars.forEach(star => {
        star.addEventListener('mouseenter', () => {
            paint(parseInt(star.dataset.value, 10));
        });

        star.addEventListener('click', () => {
            const rating = parseInt(star.dataset.value, 10);
            ratingInput.value = rating;
            paint(rating);
        });
    });

    document.querySelector('.stars').addEventListener('mouseleave', () => {
        paint(parseInt(ratingInput.value || '0', 10));
    });
</script>
@endsection