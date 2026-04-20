@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-1">Super Admin Revenue</h2>
            <p class="text-muted mb-0">Subscription state and provider distribution snapshot.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('dashboard.super-admin') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
            <a href="{{ route('dashboard.super-admin.schools') }}" class="btn btn-primary">Schools</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Total Subscriptions</h6><h3>{{ $totalSubscriptions }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Active</h6><h3>{{ $activeSubscriptions }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Trialing</h6><h3>{{ $trialSubscriptions }}</h3></div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Canceled</h6><h3>{{ $canceledSubscriptions }}</h3></div></div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent">Subscriptions by Provider</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($providerBreakdown as $row)
                                    <tr>
                                        <td>{{ $row->provider ?? 'unknown' }}</td>
                                        <td>{{ $row->total }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center py-3">No provider data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent">Schools by Plan</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Total Schools</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($planBreakdown as $row)
                                    <tr>
                                        <td>{{ $row->plan ?? 'n/a' }}</td>
                                        <td>{{ $row->total }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center py-3">No plan data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
