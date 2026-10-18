@extends('website.master')
@section('title', ($title ?? 'For you') . ' | Noor-Store')
@section('content')
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">{{ $title ?? 'For you' }}</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('welcome.index') }}">Home</a></li>
        <li class="breadcrumb-item active text-white">Recommendations</li>
    </ol>
</div>
<div class="container py-5">
    <p class="noor-section-sub mb-4">Powered by Apriori cross-sell, TF-IDF upsell and item-based collaborative filtering.</p>
    <div class="row g-4">
        @forelse($recommendations as $product)
            @include('website.product-card', ['product' => $product, 'recSource' => $productId ? 'cross_sell' : 'cf'])
        @empty
            <div class="col-12 text-center text-muted py-5">
                Browse a few products or add items to your cart to get personalised picks.
                <div class="mt-3"><a href="{{ route('products.all') }}" class="btn btn-primary rounded-pill">Shop now</a></div>
            </div>
        @endforelse
    </div>
</div>
@endsection
