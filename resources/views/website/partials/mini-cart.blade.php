@if(empty($cart))
    <p class="text-muted mb-0">Your bag is empty.</p>
@else
    <ul class="list-unstyled mb-3">
        @foreach($cart as $shopId => $item)
            <li class="d-flex gap-2 align-items-center mb-3">
                <img src="{{ $item['image'] }}" alt="" width="56" height="56" class="rounded"
                     style="object-fit:contain;background:#fff"
                     onerror="this.onerror=null;this.src='{{ asset('img/default.jpeg') }}';">
                <div class="flex-grow-1">
                    <div class="small fw-semibold">{{ \Illuminate\Support\Str::limit($item['name'], 40) }}</div>
                    <div class="small text-muted">×{{ $item['quantity'] }} · ${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                </div>
            </li>
        @endforeach
    </ul>
    <div class="d-flex justify-content-between mb-3">
        <span>Subtotal</span>
        <strong>${{ number_format($totals['subtotal'], 2) }}</strong>
    </div>
    <a href="{{ route('cart.list') }}" class="btn btn-primary w-100 rounded-pill mb-2">View cart</a>
    <a href="{{ route('checkout.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">Checkout</a>
@endif
