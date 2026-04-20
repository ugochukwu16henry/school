@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-start">
        @include('layouts.left-menu')
        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
            <div class="row pt-3">
                <div class="col ps-4">
                    <h1 class="display-6 mb-3"><i class="bi bi-gear"></i> Operations</h1>
                    <p class="text-muted">Run academic setup, billing, and daily school operations.</p>

                    <div class="row g-3">
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-sliders2 me-2"></i>Academic Settings</h5>
                                    <p class="text-muted small">Sessions, semesters, course assignments, and system rules.</p>
                                    <a href="{{ url('academics/settings') }}" class="btn btn-outline-primary btn-sm">Open Settings</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-credit-card me-2"></i>Billing</h5>
                                    <p class="text-muted small">Configure plan and checkout provider for your school.</p>
                                    <a href="{{ route('billing.setup.show') }}" class="btn btn-outline-primary btn-sm">Open Billing</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-building-gear me-2"></i>School Setup</h5>
                                    <p class="text-muted small">Update school foundational setup and readiness checks.</p>
                                    <a href="{{ route('school.setup.show') }}" class="btn btn-outline-primary btn-sm">Open Setup</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-calendar-event me-2"></i>Events</h5>
                                    <p class="text-muted small">Create and maintain school calendar events.</p>
                                    <a href="{{ route('events.show') }}" class="btn btn-outline-primary btn-sm">Open Events</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-megaphone me-2"></i>Notices</h5>
                                    <p class="text-muted small">Publish announcements for staff, students, and parents.</p>
                                    <a href="{{ route('notice.create') }}" class="btn btn-outline-primary btn-sm">Open Notices</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-sort-numeric-up-alt me-2"></i>Promotions</h5>
                                    <p class="text-muted small">Promote students to new classes and sessions.</p>
                                    <a href="{{ route('promotions.index') }}" class="btn btn-outline-primary btn-sm">Open Promotions</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
</div>
@endsection
