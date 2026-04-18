@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Complete School Setup</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Set up your first academic session, class, section, and course.</p>

                    <form method="POST" action="{{ route('school.setup.store') }}">
                        @csrf

                        <h6 class="mb-3">Academic Session</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label" for="session_name">Session Name</label>
                                <input id="session_name" name="session_name" type="text" value="{{ old('session_name') }}" class="form-control @error('session_name') is-invalid @enderror" placeholder="2026/2027" required>
                                @error('session_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="semester_name">Semester Name</label>
                                <input id="semester_name" name="semester_name" type="text" value="{{ old('semester_name') }}" class="form-control @error('semester_name') is-invalid @enderror" placeholder="First Term" required>
                                @error('semester_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="semester_start_date">Semester Start</label>
                                <input id="semester_start_date" name="semester_start_date" type="date" value="{{ old('semester_start_date') }}" class="form-control @error('semester_start_date') is-invalid @enderror" required>
                                @error('semester_start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="semester_end_date">Semester End</label>
                                <input id="semester_end_date" name="semester_end_date" type="date" value="{{ old('semester_end_date') }}" class="form-control @error('semester_end_date') is-invalid @enderror" required>
                                @error('semester_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <h6 class="mb-3">Class Structure</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label" for="class_name">Class Name</label>
                                <input id="class_name" name="class_name" type="text" value="{{ old('class_name') }}" class="form-control @error('class_name') is-invalid @enderror" placeholder="Primary 4" required>
                                @error('class_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="section_name">Section Name</label>
                                <input id="section_name" name="section_name" type="text" value="{{ old('section_name') }}" class="form-control @error('section_name') is-invalid @enderror" placeholder="A" required>
                                @error('section_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="room_no">Room No</label>
                                <input id="room_no" name="room_no" type="text" value="{{ old('room_no') }}" class="form-control @error('room_no') is-invalid @enderror" placeholder="Block B / Room 3" required>
                                @error('room_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <h6 class="mb-3">First Course</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label" for="course_name">Course Name</label>
                                <input id="course_name" name="course_name" type="text" value="{{ old('course_name') }}" class="form-control @error('course_name') is-invalid @enderror" placeholder="Mathematics" required>
                                @error('course_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="course_type">Course Type</label>
                                <select id="course_type" name="course_type" class="form-select @error('course_type') is-invalid @enderror" required>
                                    <option value="">Select</option>
                                    <option value="compulsory" {{ old('course_type') === 'compulsory' ? 'selected' : '' }}>Compulsory</option>
                                    <option value="optional" {{ old('course_type') === 'optional' ? 'selected' : '' }}>Optional</option>
                                </select>
                                @error('course_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="attendance_type">Attendance Type</label>
                                <select id="attendance_type" name="attendance_type" class="form-select @error('attendance_type') is-invalid @enderror" required>
                                    <option value="section" {{ old('attendance_type', 'section') === 'section' ? 'selected' : '' }}>By Section</option>
                                    <option value="course" {{ old('attendance_type') === 'course' ? 'selected' : '' }}>By Course</option>
                                </select>
                                @error('attendance_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Finish Setup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
