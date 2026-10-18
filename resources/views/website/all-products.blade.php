@extends('website.master')
@section('title', 'Shop | Noor-Store')
@section('content')
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Shop</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('welcome.index') }}">Home</a></li>
        <li class="breadcrumb-item active text-white">Products</li>
    </ol>
</div>

<div class="container py-5">
    <div class="row g-4">
        <aside class="col-lg-3">
            <form method="GET" action="{{ route('products.all') }}" class="noor-filter-panel sticky-top" style="top:120px">
                <h5 class="mb-3">Filters</h5>
                <div class="mb-3">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Name or brand">
                </div>
                <div class="mb-3">
                    <label class="form-label small">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="0">All</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($categoryId == $cat->id)>{{ $cat->eng_name }}</option>
                            @foreach($cat->children as $child)
                                <option value="{{ $child->id }}" @selected($categoryId == $child->id)>— {{ $child->eng_name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="0">All</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected($brandId == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small">Min $</label>
                        <input type="number" step="0.01" name="min_price" value="{{ $minPrice }}" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Max $</label>
                        <input type="number" step="0.01" name="max_price" value="{{ $maxPrice }}" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Min rating</label>
                    <select name="min_rating" class="form-select">
                        <option value="">Any</option>
                        @foreach([4, 3, 2] as $r)
                            <option value="{{ $r }}" @selected($minRating == $r)>{{ $r }}+ stars</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Sort</label>
                    <select name="sort" class="form-select">
                        <option value="popular" @selected($sort === 'popular')>Most popular</option>
                        <option value="rating" @selected($sort === 'rating')>Top rated</option>
                        <option value="newest" @selected($sort === 'newest')>Newest</option>
                        <option value="price_asc" @selected($sort === 'price_asc')>Price: low to high</option>
                        <option value="price_desc" @selected($sort === 'price_desc')>Price: high to low</option>
                        <option value="name" @selected($sort === 'name')>Name</option>
                    </select>
                </div>
                <button class="btn btn-primary w-100 rounded-pill" type="submit">Apply</button>
                <a href="{{ route('products.all') }}" class="btn btn-link w-100">Reset</a>
            </form>
        </aside>
        <div class="col-lg-9">
            <p class="text-muted mb-4">{{ $products->total() }} products</p>
            <div class="row g-4">
                @forelse($products as $product)
                    @include('website.product-card', ['product' => $product])
                @empty
                    <div class="col-12 text-center text-muted py-5">No products match your filters.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
