@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-1">Super Admin Dashboard</h2>
            <p class="text-muted mb-0">Platform-wide overview for the app owner.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('dashboard.super-admin.schools') }}" class="btn btn-outline-primary">Schools</a>
            <a href="{{ route('dashboard.super-admin.revenue') }}" class="btn btn-primary">Revenue</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Total Schools</h6><h3>{{ $schoolCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Total Users</h6><h3>{{ $userCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Admins</h6><h3>{{ $adminCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Teachers</h6><h3>{{ $teacherCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Students</h6><h3>{{ $studentCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Parents</h6><h3>{{ $parentCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Academic Sessions</h6><h3>{{ $sessionCount }}</h3></div></div>
        </div>
    </div>

    <div class="alert alert-info" role="alert">
        This is the first implementation step. Next iterations will add cross-school billing, subscription health, and school lifecycle controls.
    </div>
</div>
@endsection
