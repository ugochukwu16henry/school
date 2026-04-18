@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-1">Student Dashboard</h2>
            <p class="text-muted mb-0">Track your classes, teachers, and results.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Current Class</h6><h5>{{ optional(optional($promotion)->schoolClass)->class_name ?? 'Not assigned' }}</h5></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Current Section</h6><h5>{{ optional(optional($promotion)->section)->section_name ?? 'Not assigned' }}</h5></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Teachers</h6><h3>{{ $teacherCount }}</h3></div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Courses</h6><h3>{{ $courseCount }}</h3></div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Published Results</h6><h3>{{ $resultCount }}</h3></div></div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('course.student.list.show', ['student_id' => Auth::id()]) }}">My Courses</a></div>
        <div class="col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('student.attendance.show', ['id' => Auth::id()]) }}">My Attendance</a></div>
        <div class="col-md-4"><button type="button" class="btn btn-outline-secondary w-100" disabled>Download Transcript (next step)</button></div>
    </div>
</div>
@endsection
