@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-1">Super Admin Schools</h2>
            <p class="text-muted mb-0">Cross-school visibility for tenant health and activity.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('dashboard.super-admin') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
            <a href="{{ route('dashboard.super-admin.revenue') }}" class="btn btn-primary">Revenue</a>
        </div>
    </div>

    @php
        $schoolsOnPage = $schools->count();
        $activeSchoolsOnPage = $schools->where('status', 'active')->count();
        $totalUsersOnPage = (int) $schools->sum('users_count');
        $activeSubscriptionsOnPage = (int) $schools->sum('active_subscription_count');
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body"><h6 class="text-muted">Total Schools</h6><h3>{{ $schools->total() }}</h3></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body"><h6 class="text-muted">Schools On This Page</h6><h3>{{ $schoolsOnPage }}</h3></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body"><h6 class="text-muted">Active (This Page)</h6><h3>{{ $activeSchoolsOnPage }}</h3></div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body"><h6 class="text-muted">Users / Active Subscriptions</h6><h3>{{ $totalUsersOnPage }} / {{ $activeSubscriptionsOnPage }}</h3></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-transparent">Schools</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>School</th>
                            <th>Status</th>
                            <th>Plan</th>
                            <th>Users</th>
                            <th>Admins</th>
                            <th>Teachers</th>
                            <th>Students</th>
                            <th>Active Subscriptions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $school->name }}</div>
                                    <div class="small text-muted">{{ $school->slug }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark">{{ $school->status ?? 'unknown' }}</span></td>
                                <td><span class="badge bg-secondary">{{ $school->plan ?? 'n/a' }}</span></td>
                                <td>{{ $school->users_count }}</td>
                                <td>{{ $school->admin_count }}</td>
                                <td>{{ $school->teacher_count }}</td>
                                <td>{{ $school->student_count }}</td>
                                <td>{{ $school->active_subscription_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-3">No schools found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent">{{ $schools->links() }}</div>
    </div>
</div>
@endsection
