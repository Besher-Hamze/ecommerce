@extends('website.master')
@section('title', 'Cart | Noor-Store')
@section('content')
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Cart</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('welcome.index') }}">Home</a></li>
        <li class="breadcrumb-item active text-white">Cart</li>
    </ol>
</div>

<div class="container-fluid py-5">
    <div class="container py-5">
        @php
            $progress = $totals['free_threshold'] > 0
                ? min(100, round(100 * $totals['subtotal'] / $totals['free_threshold']))
                : 100;
            $remaining = max(0, $totals['free_threshold'] - $totals['subtotal']);
        @endphp

        @if(count($cart))
            <div class="mb-4 p-3 rounded-3" style="background:var(--noor-cream)">
                @if($totals['free'])
                    <p class="mb-2 fw-semibold text-primary">You’ve unlocked free shipping!</p>
                @else
                    <p class="mb-2">Add <strong>${{ number_format($remaining, 2) }}</strong> more for free shipping.</p>
                @endif
                <div class="noor-shipping-bar"><span id="ship-bar" style="width:{{ $progress }}%"></span></div>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cart as $shopProductId => $item)
                        <tr data-id="{{ $shopProductId }}">
                            <td>
                                <img src="{{ $item['image'] }}" class="rounded" width="72" height="72"
                                     style="object-fit:contain;background:#fff"
                                     onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';" alt="">
                            </td>
                            <td>
                                <a href="{{ route('product.details', $item['id']) }}" class="text-dark text-decoration-none">
                                    {{ $item['name'] }}
                                </a>
                                @if(!empty($upsells[$item['id']]))
                                    @php $up = $upsells[$item['id']]; @endphp
                                    <div class="noor-upsell mt-2 small">
                                        <strong>Upgrade:</strong> {{ $up->eng_name }}
                                        · ${{ number_format($up->price, 2) }}
                                        @if(!empty($up->rec_meta['uplift_pct']))
                                            <span class="text-muted">(+{{ round($up->rec_meta['uplift_pct']) }}%)</span>
                                        @endif
                                        <form action="{{ route('cart.swap') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="from" value="{{ $shopProductId }}">
                                            <input type="hidden" name="to" value="{{ $up->shop_product_id }}">
                                            <input type="hidden" name="context" value="{{ $item['id'] }}">
                                            <button class="btn btn-sm btn-primary rounded-pill ms-1">Swap</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                            <td>${{ number_format($item['price'], 2) }}</td>
                            <td>
                                <div class="input-group quantity" style="width: 110px;">
                                    <button class="btn btn-sm btn-minus rounded-circle bg-light border update-cart_decrement"
                                            data-route="{{ route('update.cart') }}" type="button"><i class="fa fa-minus"></i></button>
                                    <input type="text" class="form-control form-control-sm text-center border-0 quantity_2"
                                           value="{{ $item['quantity'] }}" min="1">
                                    <button class="btn btn-sm btn-plus rounded-circle bg-light border update-cart_increment"
                                            data-route="{{ route('update.cart') }}" type="button"><i class="fa fa-plus"></i></button>
                                </div>
                            </td>
                            <td class="line-total">${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                            <td>
                                <button class="btn btn-md rounded-circle bg-light border remove-from-cart"
                                        data-route="{{ route('remove.from.cart') }}" type="button">
                                    <i class="fa fa-times text-danger"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">Your cart is empty.
                            <a href="{{ route('products.all') }}">Continue shopping</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row g-4 justify-content-end">
            <div class="col-sm-8 col-md-7 col-lg-5">
                <div class="bg-light rounded p-4">
                    <h2 class="display-6 mb-4">Cart total</h2>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal</span>
                        <span id="cart-subtotal">${{ number_format($totals['subtotal'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping</span>
                        <span id="cart-shipping">{{ $totals['free'] ? 'Free' : '$'.number_format($totals['shipping'], 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-3 mb-4">
                        <strong>Total</strong>
                        <strong id="cart-total">${{ number_format($totals['total'], 2) }}</strong>
                    </div>
                    @if(count($cart))
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary rounded-pill px-4 py-3 text-uppercase w-100">
                            Proceed to checkout
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if($crossSell->isNotEmpty())
            <section class="mt-5">
                <h2 class="noor-section-title display-6 mb-1">Complete your look</h2>
                <p class="noor-section-sub mb-4">Frequently bought with items in your bag (Apriori + CF hybrid).</p>
                <div class="row g-4">
                    @foreach($crossSell as $product)
                        @include('website.product-card', ['product' => $product, 'recSource' => 'cross_sell'])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
window.updateShippingBar = function (totals) {
    var pct = totals.free_threshold > 0
        ? Math.min(100, Math.round(100 * totals.subtotal / totals.free_threshold)) : 100;
    var bar = document.getElementById('ship-bar');
    if (bar) bar.style.width = pct + '%';
};
</script>
@endpush
