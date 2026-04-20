@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-3">
            @include('dashboards.partials.sidebar', ['role' => 'parent'])
        </div>
        <div class="col-lg-9">
    <h2 class="mb-1">Parent Dashboard</h2>
    <p class="text-muted mb-0">View your children, academic progress, and school updates.</p>

    @php
        $linkedChildrenCount = $childrenSummary->count();
        $totalResults = (int) $childrenSummary->sum('resultCount');
        $totalAssignments = (int) $childrenSummary->sum('assignmentCount');
        $totalTeachers = (int) $childrenSummary->sum('teacherCount');
    @endphp

    <div class="row g-3 mt-1 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Linked Children</h6><h3>{{ $linkedChildrenCount }}</h3></div></div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Total Results</h6><h3>{{ $totalResults }}</h3></div></div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Total Assignments</h6><h3>{{ $totalAssignments }}</h3></div></div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card h-100"><div class="card-body"><h6 class="text-muted">Teachers In Scope</h6><h3>{{ $totalTeachers }}</h3></div></div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-people me-2"></i> Linked Children</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class/Section</th>
                            <th>Results</th>
                            <th>Assignments</th>
                            <th>Teachers</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($childrenSummary as $child)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ optional($child['student'])->first_name }} {{ optional($child['student'])->last_name }}</div>
                                    <div class="small text-muted">{{ optional($child['student'])->email ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($child['className'] && $child['sectionName'])
                                        <span class="badge bg-light text-dark">{{ $child['className'] }} / {{ $child['sectionName'] }}</span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-dark rounded-pill">{{ $child['resultCount'] }}</span></td>
                                <td><span class="badge bg-primary rounded-pill">{{ $child['assignmentCount'] }}</span></td>
                                <td><span class="badge bg-secondary rounded-pill">{{ $child['teacherCount'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-3">No linked child found yet. Ask the school to verify your parent linkage details.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent"><i class="bi bi-megaphone me-2"></i> Recent Notices</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentNotices as $notice)
                            <div class="list-group-item">
                                <div class="small text-muted">{{ $notice->created_at }}</div>
                                <div>{!! Purify::clean($notice->notice) !!}</div>
                            </div>
                        @empty
                            <div class="p-3 text-muted">No notices available.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-transparent"><i class="bi bi-calendar-event me-2"></i> Upcoming Events</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingEvents as $event)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $event->title }}</div>
                                    <div class="small text-muted">Starts: {{ $event->start }}</div>
                                </div>
                                @if($event->end)
                                    <span class="badge bg-light text-dark">Ends: {{ $event->end }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="p-3 text-muted">No upcoming events.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>
</div>
@endsection
