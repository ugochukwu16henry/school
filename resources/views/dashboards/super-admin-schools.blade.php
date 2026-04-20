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
