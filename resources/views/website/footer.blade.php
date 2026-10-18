<div class="container-fluid bg-dark text-white-50 footer pt-5 mt-5">
    <div class="container py-5">
        <div class="pb-4 mb-4" style="border-bottom: 1px solid rgba(243, 198, 207, 0.35);">
            <div class="row g-4 align-items-end">
                <div class="col-lg-6">
                    <a href="{{ route('welcome.index') }}">
                        <h1 class="text-primary mb-2">{{ $storeSettings['name'] ?? 'Noor-Store' }}</h1>
                    </a>
                    <p class="mb-0 text-white-50">{{ $storeSettings['about'] ?? '' }}</p>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <p class="mb-0">{{ $storeSettings['work_time'] ?? '' }}</p>
                </div>
            </div>
        </div>
        <div class="row g-5">
            <div class="col-lg-4 col-md-6">
                <div class="footer-item">
                    <h4 class="text-light mb-3">Shop smart</h4>
                    <p class="mb-4">Cross-sell, upsell and personalised picks built from real shopping baskets — so every recommendation earns its place in your routine.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="d-flex flex-column text-start footer-item">
                    <h4 class="text-light mb-3">Account</h4>
                    <a class="btn-link" href="{{ route('profile.show') }}">My Account</a>
                    <a class="btn-link" href="{{ route('cart.list') }}">Shopping Cart</a>
                    <a class="btn-link" href="{{ route('user.order') }}">Order History</a>
                    <a class="btn-link" href="{{ route('recommendations.index') }}">For you</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="footer-item">
                    <h4 class="text-light mb-3">Contact</h4>
                    <p>Address: {{ $storeSettings['address'] ?? '' }}</p>
                    <p>Email: {{ $storeSettings['email'] ?? '' }}</p>
                    <p>Phone: {{ $storeSettings['wats_phone'] ?? '' }}</p>
                    <p class="small">Free shipping over ${{ $storeSettings['free_shipping_threshold'] ?? 50 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
