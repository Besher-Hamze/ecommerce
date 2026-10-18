@extends('dashboard.master')

@section('content')
<section id="main-content" dir="rtl">
    <section class="wrapper site-min-height">
        <section class="panel">
            <header class="panel-heading">
                قواعد الارتباط (Apriori) — Association Rules Explorer
            </header>
            <div class="panel-body">
                <form method="GET" class="row" style="margin-bottom:20px">
                    <div class="col-md-3">
                        <label>Level</label>
                        <select name="level" class="form-control">
                            <option value="product" @selected($level === 'product')>Product</option>
                            <option value="type" @selected($level === 'type')>Product type</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Min confidence</label>
                        <input type="number" step="0.01" name="min_confidence" class="form-control" value="{{ request('min_confidence') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Min lift</label>
                        <input type="number" step="0.1" name="min_lift" class="form-control" value="{{ request('min_lift', 1) }}">
                    </div>
                    <div class="col-md-3" style="margin-top:25px">
                        <button class="btn btn-primary" type="submit">Filter</button>
                        <a href="{{ route('admin.recommendations.index') }}" class="btn btn-default">Dashboard</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" style="direction:ltr;text-align:left">
                        <thead>
                            <tr>
                                <th>Antecedent</th>
                                <th>⇒</th>
                                <th>Consequent</th>
                                <th>Support</th>
                                <th>Confidence</th>
                                <th>Lift</th>
                                <th>Leverage</th>
                                <th>Conviction</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rules as $rule)
                                <tr>
                                    <td>
                                        @foreach($rule->antecedent ?? [] as $id)
                                            <span class="label label-info">
                                                {{ $level === 'product' ? ($names[$id] ?? $id) : $id }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>⇒</td>
                                    <td>
                                        @foreach($rule->consequent ?? [] as $id)
                                            <span class="label label-success">
                                                {{ $level === 'product' ? ($names[$id] ?? $id) : $id }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>{{ number_format($rule->support, 4) }}</td>
                                    <td>{{ number_format($rule->confidence, 3) }}</td>
                                    <td>{{ number_format($rule->lift, 2) }}</td>
                                    <td>{{ number_format($rule->leverage, 4) }}</td>
                                    <td>{{ $rule->conviction !== null ? number_format($rule->conviction, 2) : '∞' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $rules->links() }}
            </div>
        </section>
    </section>
</section>
@endsection
