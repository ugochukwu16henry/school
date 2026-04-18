<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolSetupController extends Controller
{
    public function show()
    {
        $schoolId = Auth::user()->school_id;

        $isSetupComplete = DB::table('school_sessions')
            ->where('school_id', $schoolId)
            ->exists();

        if ($isSetupComplete) {
            return redirect()->route('dashboard.admin');
        }

        return view('schools.setup');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_name' => ['required', 'string', 'max:100'],
            'semester_name' => ['required', 'string', 'max:100'],
            'semester_start_date' => ['required', 'date'],
            'semester_end_date' => ['required', 'date', 'after_or_equal:semester_start_date'],
            'class_name' => ['required', 'string', 'max:100'],
            'section_name' => ['required', 'string', 'max:100'],
            'room_no' => ['required', 'string', 'max:100'],
            'course_name' => ['required', 'string', 'max:100'],
            'course_type' => ['required', 'in:optional,compulsory'],
            'attendance_type' => ['required', 'in:section,course'],
        ]);

        $schoolId = Auth::user()->school_id;
        $now = now();

        DB::transaction(function () use ($validated, $schoolId, $now) {
            $sessionId = DB::table('school_sessions')->insertGetId([
                'school_id' => $schoolId,
                'session_name' => $validated['session_name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $semesterId = DB::table('semesters')->insertGetId([
                'school_id' => $schoolId,
                'semester_name' => $validated['semester_name'],
                'start_date' => $validated['semester_start_date'],
                'end_date' => $validated['semester_end_date'],
                'session_id' => $sessionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $classId = DB::table('school_classes')->insertGetId([
                'school_id' => $schoolId,
                'class_name' => $validated['class_name'],
                'session_id' => $sessionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $sectionId = DB::table('sections')->insertGetId([
                'school_id' => $schoolId,
                'section_name' => $validated['section_name'],
                'room_no' => $validated['room_no'],
                'class_id' => $classId,
                'session_id' => $sessionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('courses')->insertGetId([
                'school_id' => $schoolId,
                'course_name' => $validated['course_name'],
                'course_type' => $validated['course_type'],
                'class_id' => $classId,
                'semester_id' => $semesterId,
                'session_id' => $sessionId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $academicSettingExists = DB::table('academic_settings')
                ->where('school_id', $schoolId)
                ->exists();

            if (!$academicSettingExists) {
                DB::table('academic_settings')->insert([
                    'school_id' => $schoolId,
                    'attendance_type' => $validated['attendance_type'],
                    'marks_submission_status' => 'off',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        return redirect()->route('dashboard.admin')->with('success', 'School setup completed successfully.');
    }
}
