@extends('website.master')
@section('title', ($product->eng_name ?? $product->name) . ' | Noor-Store')
@section('content')
@php
    $img = \App\Models\Product::imageUrl($product->image_url);
    $colors = json_decode($product->product_colors ?? '[]', true) ?: [];
@endphp

<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">{{ \Illuminate\Support\Str::limit($product->eng_name, 40) }}</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('welcome.index') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.all') }}">Shop</a></li>
        <li class="breadcrumb-item active text-white">Detail</li>
    </ol>
</div>

<div class="container py-5">
    <div class="row g-5 mb-5">
        <div class="col-lg-5">
            <div class="bg-light rounded-3 p-4 d-flex align-items-center justify-content-center" style="min-height:360px">
                <img src="{{ $img }}" alt="{{ $product->name }}" class="img-fluid"
                     style="max-height:340px;object-fit:contain"
                     onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';">
            </div>
        </div>
        <div class="col-lg-7">
            @if($product->brand_name)
                <div class="text-uppercase small text-muted mb-1">{{ $product->brand_name }}</div>
            @endif
            <h1 class="h2 fw-bold mb-2">{{ $product->eng_name }}</h1>
            <p class="mb-2 text-muted">{{ $product->category_name }}
                @if($product->form) · {{ $product->form }} @endif
            </p>
            @include('website.partials.stars', ['rating' => $product->rating_avg, 'count' => $product->rating_count])
            @if($product->price > 0)
                <h3 class="fw-bold my-3 text-primary">${{ number_format($product->price, 2) }}</h3>
            @endif

            @if(count($colors))
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach($colors as $color)
                        <div title="{{ $color['colour_name'] ?? '' }}"
                             style="width:28px;height:28px;border-radius:50%;border:1px solid #ccc;background:{{ $color['hex_value'] ?? '#eee' }}"></div>
                    @endforeach
                </div>
            @endif

            <div class="d-flex align-items-center gap-3 mb-4">
                @if($product->price > 0)
                    <input type="number" name="qty" id="qty" class="form-control" value="1" min="1" style="width:90px">
                    <input type="hidden" id="route" value="{{ route('cart.store', $product->id) }}">
                    <input type="hidden" id="rec_source" value="{{ request('rec') }}">
                    <input type="hidden" id="rec_context" value="{{ request('ctx') }}">
                    <button class="btn btn-primary rounded-pill px-4" type="button" id="btn">
                        <i class="fa fa-shopping-cart me-1"></i> Add to cart
                    </button>
                @else
                    <button class="btn btn-secondary rounded-pill" disabled>Out of stock</button>
                @endif
            </div>

            @if($upsells->isNotEmpty())
                @php $up = $upsells->first(); @endphp
                <div class="noor-upsell mb-4">
                    <div class="fw-semibold mb-1">Consider upgrading</div>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <img src="{{ $up->image }}" width="56" height="56" class="rounded" style="object-fit:contain;background:#fff"
                             onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';" alt="">
                        <div class="flex-grow-1">
                            <a href="{{ route('product.details', $up->id) }}?rec=upsell&ctx={{ $product->product_id }}" class="text-dark fw-semibold text-decoration-none">
                                {{ $up->eng_name }}
                            </a>
                            <div class="small text-muted">
                                ${{ number_format($up->price, 2) }}
                                @if(!empty($up->rec_meta['uplift_pct']))
                                    · +{{ round($up->rec_meta['uplift_pct']) }}% vs this item
                                @endif
                                @if(!empty($up->rec_meta['rating_to']))
                                    · rated {{ number_format($up->rec_meta['rating_to'], 1) }}
                                @endif
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm border border-secondary rounded-pill text-primary add-to-cart"
                                data-route="{{ route('cart.store', $up->shop_product_id) }}"
                                data-source="upsell" data-context="{{ $product->product_id }}">Add upgrade</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($bundle->isNotEmpty())
        <div class="noor-bundle mb-5">
            <h2 class="h4 noor-section-title mb-1">Frequently bought together</h2>
            <p class="noor-section-sub mb-3">Cross-sell from Apriori association rules (hybrid with CF).</p>
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                <div class="text-center" style="width:100px">
                    <img src="{{ $img }}" class="rounded mb-1" width="80" height="80" style="object-fit:contain;background:#fff" alt="">
                    <div class="small">This item</div>
                </div>
                @foreach($bundle as $item)
                    <span class="fs-4 text-muted">+</span>
                    <div class="text-center" style="width:100px">
                        <a href="{{ route('product.details', $item->id) }}?rec=bundle&ctx={{ $product->product_id }}">
                            <img src="{{ $item->image }}" class="rounded mb-1" width="80" height="80"
                                 style="object-fit:contain;background:#fff"
                                 onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';" alt="">
                        </a>
                        <div class="small">{{ \Illuminate\Support\Str::limit($item->eng_name, 28) }}</div>
                        <div class="small fw-semibold">${{ number_format($item->price, 2) }}</div>
                    </div>
                @endforeach
            </div>
            @php
                $bundleIds = $bundle->pluck('shop_product_id')->prepend($product->id)->values()->all();
                $bundleTotal = $product->price + $bundle->sum('price');
            @endphp
            <button type="button" class="btn btn-primary rounded-pill add-bundle"
                    data-route="{{ route('cart.bundle') }}"
                    data-ids='@json($bundleIds)'
                    data-context="{{ $product->product_id }}">
                Add all · ${{ number_format($bundleTotal, 2) }}
            </button>
        </div>
    @endif

    <nav>
        <div class="nav nav-tabs mb-3">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-about" type="button">Description</button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-mission" type="button">
                Reviews ({{ $product->reviews->count() }})
            </button>
        </div>
    </nav>
    <div class="tab-content mb-5">
        <div class="tab-pane fade show active" id="nav-about">
            <p>{{ $product->description }}</p>
        </div>
        <div class="tab-pane fade" id="nav-mission">
            @forelse($product->reviews as $review)
                <div class="d-flex mb-4 gap-3">
                    <div>
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <h5 class="mb-0">{{ $review->user->name ?? 'Customer' }}</h5>
                            @include('website.partials.stars', ['rating' => $review->star, 'count' => 0])
                        </div>
                        <p class="small text-muted mb-1">{{ $review->created_at?->format('M d, Y') }}</p>
                        <p>{{ $review->review }}</p>
                    </div>
                </div>
            @empty
                <p class="text-muted">No reviews yet.</p>
            @endforelse

            @auth
                <form action="{{ route('review.store') }}" method="POST" class="mt-4">
                    @csrf
                    <h4 class="mb-3">Leave a review</h4>
                    <textarea name="review" class="form-control mb-3" rows="4" placeholder="Your review"></textarea>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span>Rating:</span>
                        <div class="stars" style="font-size:1.5rem;cursor:pointer;color:#D9C4CC">
                            @for($i=1;$i<=5;$i++)
                                <span class="star" data-value="{{ $i }}">&#9733;</span>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="rating" required>
                    </div>
                    <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                    <button class="btn btn-primary rounded-pill" type="submit">Post review</button>
                </form>
            @endauth
        </div>
    </div>

    @if($alsoLiked->isNotEmpty())
        <section class="mb-5">
            <h2 class="noor-section-title display-6 mb-1">Customers also liked</h2>
            <p class="noor-section-sub mb-4">Item-based collaborative filtering.</p>
            <div class="row g-4">
                @foreach($alsoLiked as $item)
                    @include('website.product-card', ['product' => $item, 'recSource' => 'cf', 'recContext' => $product->product_id])
                @endforeach
            </div>
        </section>
    @endif

    @if($similar->isNotEmpty())
        <section class="mb-5">
            <h2 class="noor-section-title display-6 mb-1">Similar products</h2>
            <p class="noor-section-sub mb-4">Content similarity (TF-IDF).</p>
            <div class="row g-4">
                @foreach($similar as $item)
                    @include('website.product-card', ['product' => $item, 'recSource' => 'similar', 'recContext' => $product->product_id])
                @endforeach
            </div>
        </section>
    @endif

    @if($recentlyViewed->isNotEmpty())
        <section class="mb-5">
            <h2 class="noor-section-title display-6 mb-3">Recently viewed</h2>
            <div class="row g-4">
                @foreach($recentlyViewed as $item)
                    @include('website.product-card', ['product' => $item])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    const stars = document.querySelectorAll('.star');
    const ratingInput = document.getElementById('rating');
    if (!stars.length || !ratingInput) return;
    function paint(rating) {
        stars.forEach(s => s.style.color = parseInt(s.dataset.value, 10) <= rating ? '#E8A838' : '#D9C4CC');
    }
    stars.forEach(star => {
        star.addEventListener('mouseenter', () => paint(parseInt(star.dataset.value, 10)));
        star.addEventListener('click', () => {
            ratingInput.value = star.dataset.value;
            paint(parseInt(star.dataset.value, 10));
        });
    });
    document.querySelector('.stars')?.addEventListener('mouseleave', () => paint(parseInt(ratingInput.value || '0', 10)));
})();
</script>
@endpush
