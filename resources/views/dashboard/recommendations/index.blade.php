@extends('dashboard.master')

@section('content')
<section id="main-content" dir="rtl">
    <section class="wrapper site-min-height">
        <section class="panel">
            <header class="panel-heading">
                محرك التوصيات — Recommendation Engine
            </header>
            <div class="panel-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-info">
                            <div class="panel-body text-center">
                                <h5>Cross-sell coverage</h5>
                                <h2>{{ $coveragePct['cross_sell'] }}%</h2>
                                <small>{{ $coverage['cross_sell'] }} / {{ $productCount }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-success">
                            <div class="panel-body text-center">
                                <h5>Upsell coverage</h5>
                                <h2>{{ $coveragePct['upsell'] }}%</h2>
                                <small>{{ $coverage['upsell'] }} / {{ $productCount }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-warning">
                            <div class="panel-body text-center">
                                <h5>CF coverage</h5>
                                <h2>{{ $coveragePct['cf'] }}%</h2>
                                <small>{{ $coverage['cf'] }} / {{ $productCount }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h5>Association rules</h5>
                                <h2>{{ $rulesCount['product'] + $rulesCount['type'] }}</h2>
                                <small>{{ $rulesCount['product'] }} product · {{ $rulesCount['type'] }} type</small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h4>إعادة التدريب / التقييم</h4>
                <div class="row">
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.recommendations.train') }}" class="form-inline">
                            @csrf
                            <div class="form-group" style="margin-left:10px">
                                <label>min support</label>
                                <input type="number" step="0.001" name="min_support" class="form-control" placeholder="0.01">
                            </div>
                            <div class="form-group" style="margin-left:10px">
                                <label>min confidence</label>
                                <input type="number" step="0.01" name="min_confidence" class="form-control" placeholder="0.2">
                            </div>
                            <button class="btn btn-primary" type="submit" style="margin-right:10px">Train now</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.recommendations.evaluate') }}" class="form-inline">
                            @csrf
                            <div class="form-group" style="margin-left:10px">
                                <label>K</label>
                                <input type="number" name="k" value="5" class="form-control" style="width:80px">
                            </div>
                            <button class="btn btn-info" type="submit" style="margin-right:10px">Evaluate</button>
                        </form>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h4>آخر تدريب</h4>
                        @if($latestTrain)
                            <p><strong>Status:</strong> {{ $latestTrain->status }} · {{ $latestTrain->duration_ms }} ms</p>
                            <p><strong>At:</strong> {{ $latestTrain->created_at }}</p>
                            <pre style="max-height:220px;overflow:auto;background:#f7f7f7;padding:10px;direction:ltr;text-align:left">{{ json_encode($latestTrain->stats, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="text-muted">لا يوجد تدريب بعد. شغّل Train now.</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h4>آخر تقييم (offline)</h4>
                        @if($latestEval)
                            <p><strong>At:</strong> {{ $latestEval->created_at }} · {{ $latestEval->duration_ms }} ms</p>
                            <pre style="max-height:220px;overflow:auto;background:#f7f7f7;padding:10px;direction:ltr;text-align:left">{{ json_encode($latestEval->stats, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            <p class="text-muted">لا يوجد تقييم بعد.</p>
                        @endif
                    </div>
                </div>

                <hr>
                <h4>أداء الأونلاين (آخر 30 يوم)</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" style="direction:ltr;text-align:left">
                        <thead>
                            <tr>
                                <th>Source</th>
                                <th>Impressions</th>
                                <th>Clicks</th>
                                <th>CTR %</th>
                                <th>Add to cart</th>
                                <th>Conv %</th>
                                <th>Purchases</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $source => $row)
                                <tr>
                                    <td>{{ $source }}</td>
                                    <td>{{ $row['impressions'] }}</td>
                                    <td>{{ $row['clicks'] }}</td>
                                    <td>{{ $row['ctr'] }}</td>
                                    <td>{{ $row['add_to_cart'] }}</td>
                                    <td>{{ $row['conversion'] }}</td>
                                    <td>{{ $row['purchases'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7">No events yet — browse the storefront to generate impressions.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p>
                    <a href="{{ route('admin.association-rules.index') }}" class="btn btn-default">استكشاف قواعد Apriori</a>
                </p>
            </div>
        </section>
    </section>
</section>
@endsection
