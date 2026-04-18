@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Billing Setup</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Pricing (Fair-Growth Model)</p>
                    <ul class="small text-muted mb-4 ps-3">
                        <li>Start free: first 50 students at N0. We grow when your enrolment grows across Africa.</li>
                        <li>Monthly: N100 per billable student per month beyond the first 50.</li>
                        <li>One-time activation: N500 per new student added beyond the first 50.</li>
                        <li>No hidden fees: includes portal, parent and staff access, and ongoing updates.</li>
                    </ul>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if(session('checkout_url'))
                        <div class="alert alert-info">
                            Checkout URL placeholder: <a href="{{ session('checkout_url') }}" target="_blank">{{ session('checkout_url') }}</a>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Current Status:</strong> {{ $subscription->status }}<br>
                        <strong>Current Plan:</strong> {{ $subscription->plan }}<br>
                        <strong>Trial Ends:</strong> {{ optional($subscription->trial_ends_at)->toDateString() ?? '-' }}
                    </div>

                    <form method="POST" action="{{ route('billing.setup.checkout') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="plan" class="form-label">Plan</label>
                                <select id="plan" name="plan" class="form-select @error('plan') is-invalid @enderror" required>
                                    <option value="fair_growth" {{ old('plan', $subscription->plan) === 'fair_growth' ? 'selected' : '' }}>Fair-Growth</option>
                                </select>
                                @error('plan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="provider" class="form-label">Payment Provider</label>
                                <select id="provider" name="provider" class="form-select @error('provider') is-invalid @enderror" required>
                                    <option value="stripe" {{ old('provider', $subscription->provider) === 'stripe' ? 'selected' : '' }}>Stripe</option>
                                    <option value="paystack" {{ old('provider', $subscription->provider) === 'paystack' ? 'selected' : '' }}>Paystack</option>
                                </select>
                                @error('provider')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary">Skip for now</a>
                            <button type="submit" class="btn btn-primary">Start Checkout</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
