@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-start">
        @include('layouts.left-menu')
        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
            <div class="row pt-3">
                <div class="col ps-4">
                    <h1 class="display-6 mb-3"><i class="bi bi-people"></i> People</h1>
                    <p class="text-muted">Manage students, teachers, and relationships from one place.</p>

                    <div class="row g-3">
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-mortarboard me-2"></i>Students</h5>
                                    <p class="text-muted small">Current count: {{ $studentCount }}</p>
                                    <a href="{{ route('student.list.show') }}" class="btn btn-outline-primary btn-sm">View Students</a>
                                    @if (!session()->has('browse_session_id'))
                                        <a href="{{ route('student.create.show') }}" class="btn btn-primary btn-sm mt-2">Add Student</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-person-workspace me-2"></i>Teachers</h5>
                                    <p class="text-muted small">Current count: {{ $teacherCount }}</p>
                                    <a href="{{ route('teacher.list.show') }}" class="btn btn-outline-primary btn-sm">View Teachers</a>
                                    @if (!session()->has('browse_session_id'))
                                        <a href="{{ route('teacher.create.show') }}" class="btn btn-primary btn-sm mt-2">Add Teacher</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-people-fill me-2"></i>Parents</h5>
                                    <p class="text-muted small">Current count: {{ $parentCount }}</p>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled>Managed via linkage</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-journal-bookmark me-2"></i>Classes</h5>
                                    <p class="text-muted small">Current count: {{ $classCount }}</p>
                                    <a href="{{ url('classes') }}" class="btn btn-outline-primary btn-sm">Open Classes</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-arrow-left-right me-2"></i>Promotions</h5>
                                    <p class="text-muted small">Move students to next level</p>
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
