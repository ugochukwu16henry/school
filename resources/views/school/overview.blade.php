@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-start">
        @include('layouts.left-menu')
        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-10 col-xl-10 col-xxl-10">
            <div class="row pt-3">
                <div class="col ps-4">
                    <h1 class="display-6 mb-3"><i class="bi bi-speedometer2"></i> School Overview</h1>
                    <p class="text-muted">Core KPIs and quick actions for school administrators.</p>

                    <div class="row dashboard g-3">
                        <div class="col-md-4">
                            <div class="card rounded-3 h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><i class="bi bi-person-lines-fill me-2"></i> Total Students</div>
                                        <div class="text-muted small">Current session</div>
                                    </div>
                                    <span class="badge bg-dark rounded-pill">{{ $studentCount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card rounded-3 h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><i class="bi bi-person-workspace me-2"></i> Total Teachers</div>
                                        <div class="text-muted small">Active staff</div>
                                    </div>
                                    <span class="badge bg-dark rounded-pill">{{ $teacherCount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card rounded-3 h-100">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold"><i class="bi bi-diagram-3 me-2"></i> Total Classes</div>
                                        <div class="text-muted small">Configured classes</div>
                                    </div>
                                    <span class="badge bg-dark rounded-pill">{{ $classCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 g-3">
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header bg-transparent"><i class="bi bi-lightning me-2"></i> Quick Actions</div>
                                <div class="list-group list-group-flush">
                                    <a href="{{ route('school.people') }}" class="list-group-item list-group-item-action"><i class="bi bi-people me-2"></i> Manage People</a>
                                    <a href="{{ route('school.operations') }}" class="list-group-item list-group-item-action"><i class="bi bi-gear me-2"></i> Manage Operations</a>
                                    <a href="{{ route('events.show') }}" class="list-group-item list-group-item-action"><i class="bi bi-calendar-event me-2"></i> Open Events</a>
                                    <a href="{{ route('notice.create') }}" class="list-group-item list-group-item-action"><i class="bi bi-megaphone me-2"></i> Publish Notice</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-megaphone me-2"></i> Latest Notices</span>
                                    <small class="text-muted">{{ $notices->total() }} total</small>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @forelse ($notices->take(5) as $notice)
                                            <div class="list-group-item">
                                                <div class="small text-muted">{{ $notice->created_at }}</div>
                                                <div>{!! Purify::clean($notice->notice) !!}</div>
                                            </div>
                                        @empty
                                            <div class="p-3 text-muted">No notices yet.</div>
                                        @endforelse
                                    </div>
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
