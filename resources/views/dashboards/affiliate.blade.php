@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-1">Affiliate Dashboard</h2>
            <p class="text-muted mb-0">Track referrals, payout status, and training progress.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Referred Schools</h6><h3>{{ $referredSchoolCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Pending Payout Items</h6><h3>{{ $pendingPayoutCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Training Progress</h6><h3>{{ $trainingProgress }}%</h3></div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-transparent">Next Integration Steps</div>
        <div class="card-body">
            <ul class="mb-0">
                <li>Wire referred-school records to onboarding/referral tracking.</li>
                <li>Add payout settings and payout history views.</li>
                <li>Add training content completion tracking for affiliates.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
