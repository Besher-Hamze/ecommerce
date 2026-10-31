@php
    $rec = $recSource ?? null;
    $ctx = $recContext ?? null;
    $detailUrl = route('product.details', $product->id) . ($rec ? '?rec=' . urlencode($rec) . ($ctx ? '&ctx=' . $ctx : '') : '');
    $img = $product->image ?? \App\Models\Product::imageUrl($product->image_url ?? null);
@endphp
<div class="{{ $colClass ?? 'col-6 col-md-4 col-lg-3' }} d-flex">
    <div class="noor-product-card w-100 d-flex flex-column">
        <div class="thumb position-relative">
            @auth
                @php
                    $wishId = $product->shop_product_id ?? $product->id;
                    $saved = in_array((int) $wishId, $wishlistIds ?? [], true);
                @endphp
                <button type="button"
                        class="btn btn-sm btn-light rounded-circle position-absolute wishlist-toggle {{ $saved ? 'active' : '' }}"
                        style="top:10px;right:10px;z-index:2"
                        data-id="{{ $wishId }}"
                        data-route="{{ route('favorite.add', $wishId) }}"
                        title="{{ $saved ? 'Remove from wishlist' : 'Add to wishlist' }}"
                        aria-label="Toggle wishlist">
                    <i class="{{ $saved ? 'fas' : 'far' }} fa-heart {{ $saved ? 'text-danger' : 'text-muted' }}"></i>
                </button>
            @else
                <a href="{{ route('login') }}"
                   class="btn btn-sm btn-light rounded-circle position-absolute"
                   style="top:10px;right:10px;z-index:2"
                   title="Login to save"
                   aria-label="Login to save">
                    <i class="far fa-heart text-muted"></i>
                </a>
            @endauth
            <a href="{{ $detailUrl }}">
                <img src="{{ $img }}"
                     alt="{{ $product->eng_name ?? $product->name }}"
                     loading="lazy"
                     onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';">
            </a>
            @if(!empty($product->category_name))
                <span class="noor-badge position-absolute" style="top:10px;left:10px;">{{ $product->category_name }}</span>
            @endif
        </div>
        <div class="p-3 d-flex flex-column flex-grow-1">
            @if(!empty($product->brand_name))
                <div class="brand mb-1">{{ $product->brand_name }}</div>
            @endif
            <a href="{{ $detailUrl }}" class="text-decoration-none">
                <div class="title mb-2">{{ $product->eng_name ?? $product->name }}</div>
            </a>
            @include('website.partials.stars', ['rating' => $product->rating_avg ?? 0, 'count' => $product->rating_count ?? 0])
            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 gap-2">
                @if(($product->price ?? 0) > 0)
                    <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                    <button type="button"
                            class="btn btn-sm border border-secondary rounded-pill text-primary add-to-cart"
                            data-id="{{ $product->shop_product_id }}"
                            data-route="{{ route('cart.store', $product->shop_product_id) }}"
                            @if($rec) data-source="{{ $rec }}" @endif
                            @if($ctx) data-context="{{ $ctx }}" @endif>
                        <i class="fa fa-shopping-bag"></i>
                    </button>
                @else
                    <span class="text-muted small">Out of stock</span>
                @endif
            </div>
        </div>
    </div>
</div>
