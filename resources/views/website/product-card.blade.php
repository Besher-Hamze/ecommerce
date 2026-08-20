<div class="col-md-6 col-lg-4 col-xl-3 d-flex">

    <div class="rounded position-relative fruite-item d-flex flex-column w-100 h-100">

        <!-- IMAGE (قابلة للضغط) -->
        <div class="fruite-img position-relative">
            <a href="{{ route('product.details', $product->id) }}" class="d-block">
                <img src="{{ $product->image_url }}"
                    alt="{{ $product->name }}"
                    class="w-100 product-card-img"
                    onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';">
            </a>
        </div>

        <!-- CATEGORY -->
        <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
            style="top:10px; left:10px;">
            {{ $product->category->eng_name }}
        </div>

        <!-- CONTENT -->
        <div class="p-4 border border-secondary border-top-0 rounded-bottom d-flex flex-column flex-grow-1">

            <a href="{{ route('product.details', $product->id) }}"
                class="text-decoration-none text-dark">
                <h4 class="product-card-title">{{ $product->eng_name }}</h4>
            </a>
            <p class="product-card-desc">
                {{ Str::limit($product->eng_description, 80) }}
            </p>

            <div class="d-flex justify-content-between mt-auto align-items-center">

                @if($product->price > 0)
                <p class="text-dark fs-5 fw-bold mb-0">{{ $product->price }} $</p>

                <a class="btn border border-secondary rounded-pill px-3 text-primary add-to-cart"
                    data-id="{{ $product->shop_product_id }}"
                    data-route="{{ route('cart.store', $product->shop_product_id) }}"
                    type="button">
                    <i class="fa fa-shopping-bag me-2"></i> Add to cart
                </a>
                @else
                <a class="btn border border-secondary rounded-pill px-3 text-muted disabled"
                    style="pointer-events:none; opacity:0.6;">
                    <i class="fa fa-times me-2"></i> Out of stock
                </a>
                @endif

            </div>
        </div>

    </div>
</div>