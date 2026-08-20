@extends('website.master')

@section('content')
<section class="container" style="padding: 40px 0;">
    <h2 style="margin-bottom:20px;">Smart Cosmetic Recommendations</h2>

    @if($product)
    <div class="alert alert-info">
        Because customer is interested in: <strong>{{ $product->name }}</strong>
    </div>
    @endif

    <div class="row">
        @forelse($recommendations as $entry)
        @php
        $item = $entry['product'];
        $rule = $entry['rule'];
        @endphp
        <div class="col-md-3" style="margin-bottom:20px;">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h4>{{ $item->name }}</h4>
                    <a href="{{ route('product.details', $item->id) }}" class="btn btn-primary btn-sm">View Product</a>
                    <hr>
                    <small>
                        Support: {{ number_format($rule->support, 3) }} |
                        Confidence: {{ number_format($rule->confidence, 3) }} |
                        Lift: {{ number_format($rule->lift, 3) }}
                    </small>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning">
                No recommendations available for this product yet.
            </div>
        </div>
        @endforelse
    </div>
</section>
@endsection