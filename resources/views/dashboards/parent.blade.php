@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-1">Parent Dashboard</h2>
            <p class="text-muted mb-0">View your children, their classes, teachers, and results.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Linked Children</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Results</th>
                            <th>Transcript</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($children as $child)
                            <tr>
                                <td>{{ optional($child->student)->first_name }} {{ optional($child->student)->last_name }}</td>
                                <td>{{ optional($child->student)->email ?? '-' }}</td>
                                <td><span class="badge bg-light text-dark">Available in next step</span></td>
                                <td><button type="button" class="btn btn-sm btn-outline-secondary" disabled>Download</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3">No linked child found yet. Parent invite/link flow will be added next.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
