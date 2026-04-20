@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3">
            @include('dashboards.partials.sidebar', ['role' => 'teacher'])
        </div>
        <div class="col-lg-9">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-1">Teacher Dashboard</h2>
            <p class="text-muted mb-0">Your classes, courses, and quick teaching actions.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Assigned Classes</h6><h3>{{ $classCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Assigned Courses</h6><h3>{{ $courseCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Assigned Students</h6><h3>{{ $studentCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Published Assignments</h6><h3>{{ $assignmentCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Exams in Scope</h6><h3>{{ $examCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Marks Entries</h6><h3>{{ $markCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Final Marks</h6><h3>{{ $finalMarkCount }}</h3></div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Attendance Today</h6><h3>{{ $attendanceTodayCount }}</h3></div></div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('course.teacher.list.show', ['teacher_id' => Auth::id()]) }}">My Courses</a></div>
        <div class="col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('attendance.create.show') }}">Take Attendance</a></div>
        <div class="col-md-4"><a class="btn btn-outline-primary w-100" href="{{ route('course.mark.create') }}">Enter Marks</a></div>
    </div>

    <div class="card">
        <div class="card-header">Recent Assignments</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Published</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Course</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAssignments as $row)
                            <tr>
                                <td>{{ $row->created_at }}</td>
                                <td>{{ optional($row->schoolClass)->class_name ?? '-' }}</td>
                                <td>{{ optional($row->section)->section_name ?? '-' }}</td>
                                <td>{{ optional($row->course)->course_name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3">No assignment published yet.</td></tr>
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
