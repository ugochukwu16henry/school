<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FinalMark;
use App\Models\Promotion;
use App\Models\School;
use App\Models\SchoolSession;
use App\Models\AssignedTeacher;
use App\Models\StudentParentInfo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        if ($role === 'super_admin') {
            return redirect()->route('dashboard.super-admin');
        }

        if ($role === 'teacher') {
            return redirect()->route('dashboard.teacher');
        }

        if ($role === 'student') {
            return redirect()->route('dashboard.student');
        }

        if ($role === 'parent') {
            return redirect()->route('dashboard.parent');
        }

        return redirect()->route('dashboard.admin');
    }

    public function superAdmin()
    {
        $stats = [
            'schoolCount' => School::count(),
            'userCount' => User::count(),
            'adminCount' => User::where('role', 'admin')->count(),
            'teacherCount' => User::where('role', 'teacher')->count(),
            'studentCount' => User::where('role', 'student')->count(),
            'parentCount' => User::where('role', 'parent')->count(),
            'sessionCount' => SchoolSession::count(),
        ];

        return view('dashboards.super-admin', $stats);
    }

    public function teacher()
    {
        $teacherId = Auth::id();
        $schoolId = Auth::user()->school_id;

        $assigned = AssignedTeacher::with(['schoolClass', 'section', 'course'])
            ->where('teacher_id', $teacherId)
            ->where('school_id', $schoolId)
            ->orderBy('id', 'desc')
            ->get();

        $classCount = $assigned->pluck('class_id')->unique()->count();
        $courseCount = $assigned->pluck('course_id')->unique()->count();

        $pairs = $assigned
            ->map(function ($item) {
                return $item->class_id . '-' . $item->section_id;
            })
            ->unique()
            ->values();

        $studentCount = 0;

        if ($pairs->isNotEmpty()) {
            $studentCount = Promotion::where('school_id', $schoolId)
                ->get()
                ->filter(function ($promotion) use ($pairs) {
                    $key = $promotion->class_id . '-' . $promotion->section_id;

                    return $pairs->contains($key);
                })
                ->pluck('student_id')
                ->unique()
                ->count();
        }

        return view('dashboards.teacher', [
            'assigned' => $assigned,
            'classCount' => $classCount,
            'courseCount' => $courseCount,
            'studentCount' => $studentCount,
        ]);
    }

    public function student()
    {
        $studentId = Auth::id();
        $schoolId = Auth::user()->school_id;
        $currentSessionId = $this->currentSessionId();

        $promotion = Promotion::with(['schoolClass', 'section'])
            ->where('student_id', $studentId)
            ->where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->latest()
            ->first();

        $courseCount = 0;
        $teacherCount = 0;

        if ($promotion) {
            $courseCount = Course::where('class_id', $promotion->class_id)
                ->where('school_id', $schoolId)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->count();

            $teacherCount = AssignedTeacher::where('class_id', $promotion->class_id)
                ->where('section_id', $promotion->section_id)
                ->where('school_id', $schoolId)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->pluck('teacher_id')
                ->unique()
                ->count();
        }

        $resultCount = FinalMark::where('student_id', $studentId)
            ->where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->count();

        return view('dashboards.student', [
            'promotion' => $promotion,
            'courseCount' => $courseCount,
            'teacherCount' => $teacherCount,
            'resultCount' => $resultCount,
        ]);
    }

    public function parent()
    {
        $user = Auth::user();
        $schoolId = $user->school_id;

        $children = StudentParentInfo::with('student')
            ->where('school_id', $schoolId)
            ->where(function ($query) use ($user) {
                $query->where('father_phone', $user->phone)
                    ->orWhere('mother_phone', $user->phone);
            })
            ->get();

        return view('dashboards.parent', [
            'children' => $children,
        ]);
    }

    private function currentSessionId()
    {
        if (session()->has('browse_session_id')) {
            return session('browse_session_id');
        }

        $latest = SchoolSession::latest()->first();

        return $latest ? $latest->id : null;
    }
}
