@php
    $role = $role ?? (Auth::user()->role ?? 'admin');
    $linkClass = function (array $patterns) {
        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) {
                return 'list-group-item list-group-item-action active';
            }
        }

        return 'list-group-item list-group-item-action';
    };
@endphp

<div class="card shadow-sm">
    <div class="card-header bg-transparent fw-semibold">
        <i class="bi bi-layout-sidebar-inset me-2"></i>
        {{ ucfirst(str_replace('_', ' ', $role)) }} Navigation
    </div>
    <div class="list-group list-group-flush">
        @if ($role === 'super_admin')
            <a href="{{ route('dashboard.super-admin') }}" class="{{ $linkClass(['dashboard.super-admin']) }}">
                <i class="bi bi-speedometer2 me-2"></i>Overview
            </a>
            <a href="{{ route('dashboard.super-admin.schools') }}" class="{{ $linkClass(['dashboard.super-admin.schools']) }}">
                <i class="bi bi-building me-2"></i>Schools
            </a>
            <a href="{{ route('dashboard.super-admin.revenue') }}" class="{{ $linkClass(['dashboard.super-admin.revenue']) }}">
                <i class="bi bi-cash-coin me-2"></i>Revenue
            </a>
        @elseif ($role === 'teacher')
            <a href="{{ route('dashboard.teacher') }}" class="{{ $linkClass(['dashboard.teacher']) }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="{{ route('course.teacher.list.show', ['teacher_id' => Auth::id()]) }}" class="{{ $linkClass(['course.teacher.list.show']) }}">
                <i class="bi bi-journal-text me-2"></i>My Courses
            </a>
            <a href="{{ route('attendance.create.show') }}" class="{{ $linkClass(['attendance.create.show']) }}">
                <i class="bi bi-calendar-check me-2"></i>Take Attendance
            </a>
            <a href="{{ route('course.mark.create') }}" class="{{ $linkClass(['course.mark.create']) }}">
                <i class="bi bi-pencil-square me-2"></i>Enter Marks
            </a>
            <a href="{{ route('assignment.list.show') }}" class="{{ $linkClass(['assignment.list.show', 'assignment.create']) }}">
                <i class="bi bi-list-task me-2"></i>Assignments
            </a>
        @elseif ($role === 'student')
            <a href="{{ route('dashboard.student') }}" class="{{ $linkClass(['dashboard.student']) }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="{{ route('course.student.list.show', ['student_id' => Auth::id()]) }}" class="{{ $linkClass(['course.student.list.show']) }}">
                <i class="bi bi-journal-bookmark me-2"></i>My Courses
            </a>
            <a href="{{ route('student.attendance.show', ['id' => Auth::id()]) }}" class="{{ $linkClass(['student.attendance.show']) }}">
                <i class="bi bi-calendar2-week me-2"></i>My Attendance
            </a>
            <a href="{{ route('course.mark.list.show') }}" class="{{ $linkClass(['course.mark.list.show']) }}">
                <i class="bi bi-bar-chart-line me-2"></i>Results
            </a>
            <a href="{{ route('events.show') }}" class="{{ $linkClass(['events.show']) }}">
                <i class="bi bi-calendar-event me-2"></i>Events
            </a>
        @elseif ($role === 'parent')
            <a href="{{ route('dashboard.parent') }}" class="{{ $linkClass(['dashboard.parent']) }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="{{ route('events.show') }}" class="{{ $linkClass(['events.show']) }}">
                <i class="bi bi-calendar-event me-2"></i>School Events
            </a>
        @else
            <a href="{{ route('dashboard.admin') }}" class="{{ $linkClass(['dashboard.admin', 'home']) }}">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="{{ route('school.overview') }}" class="{{ $linkClass(['school.overview']) }}">
                <i class="bi bi-building-check me-2"></i>School Overview
            </a>
            <a href="{{ route('school.people') }}" class="{{ $linkClass(['school.people']) }}">
                <i class="bi bi-people me-2"></i>People
            </a>
            <a href="{{ route('school.operations') }}" class="{{ $linkClass(['school.operations']) }}">
                <i class="bi bi-diagram-3 me-2"></i>Operations
            </a>
            <a href="{{ route('attendance.index') }}" class="{{ $linkClass(['attendance.index', 'attendance.list.show', 'attendance.create.show']) }}">
                <i class="bi bi-calendar-check me-2"></i>Attendance
            </a>
            <a href="{{ route('events.show') }}" class="{{ $linkClass(['events.show']) }}">
                <i class="bi bi-calendar-event me-2"></i>Events
            </a>
        @endif
    </div>
</div>
