@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @include('session-messages')

            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">Parent Account Setup</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        You are claiming access for
                        <strong>{{ optional($claim->student)->first_name }} {{ optional($claim->student)->last_name }}</strong>.
                        Complete your information below.
                    </p>

                    <form method="POST" action="{{ route('parent.claim.store', ['code' => $code]) }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required value="{{ old('first_name', $claim->father_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email', $claim->parent_email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required value="{{ old('phone', $claim->father_phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nationality</label>
                            <input type="text" name="nationality" class="form-control" required value="{{ old('nationality') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" required value="{{ old('address', $claim->parent_address) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address 2</label>
                            <input type="text" name="address2" class="form-control" value="{{ old('address2') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" required value="{{ old('city') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Zip</label>
                            <input type="text" name="zip" class="form-control" required value="{{ old('zip') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Create Parent Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
