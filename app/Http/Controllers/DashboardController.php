<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Event;
use App\Models\FinalMark;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Notice;
use App\Models\Promotion;
use App\Models\School;
use App\Models\SchoolSession;
use App\Models\SchoolSubscription;
use App\Models\AssignedTeacher;
use App\Models\StudentParentInfo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        if ($role === 'admin') {
            $isSetupComplete = SchoolSession::where('school_id', $user->school_id)->exists();

            if (!$isSetupComplete) {
                return redirect()->route('school.setup.show');
            }

            return redirect()->route('school.overview');
        }

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

        if ($role === 'affiliate') {
            return redirect()->route('dashboard.affiliate');
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

    public function superAdminSchools()
    {
        $schools = School::withCount([
            'users',
            'users as admin_count' => function ($query) {
                $query->where('role', 'admin');
            },
            'users as teacher_count' => function ($query) {
                $query->where('role', 'teacher');
            },
            'users as student_count' => function ($query) {
                $query->where('role', 'student');
            },
            'subscriptions as active_subscription_count' => function ($query) {
                $query->where('status', 'active');
            },
        ])
            ->orderBy('name')
            ->paginate(15);

        return view('dashboards.super-admin-schools', [
            'schools' => $schools,
        ]);
    }

    public function superAdminRevenue()
    {
        $totalSubscriptions = SchoolSubscription::count();
        $activeSubscriptions = SchoolSubscription::where('status', 'active')->count();
        $trialSubscriptions = SchoolSubscription::where('status', 'trialing')->count();
        $canceledSubscriptions = SchoolSubscription::where('status', 'canceled')->count();

        $providerBreakdown = SchoolSubscription::selectRaw('provider, COUNT(*) as total')
            ->groupBy('provider')
            ->orderByDesc('total')
            ->get();

        $planBreakdown = School::selectRaw('plan, COUNT(*) as total')
            ->groupBy('plan')
            ->orderByDesc('total')
            ->get();

        return view('dashboards.super-admin-revenue', [
            'totalSubscriptions' => $totalSubscriptions,
            'activeSubscriptions' => $activeSubscriptions,
            'trialSubscriptions' => $trialSubscriptions,
            'canceledSubscriptions' => $canceledSubscriptions,
            'providerBreakdown' => $providerBreakdown,
            'planBreakdown' => $planBreakdown,
        ]);
    }

    public function teacher()
    {
        $teacherId = Auth::id();
        $schoolId = Auth::user()->school_id;
        $currentSessionId = $this->currentSessionId();

        $assigned = AssignedTeacher::with(['schoolClass', 'section', 'course'])
            ->where('teacher_id', $teacherId)
            ->where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
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
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->get()
                ->filter(function ($promotion) use ($pairs) {
                    $key = $promotion->class_id . '-' . $promotion->section_id;

                    return $pairs->contains($key);
                })
                ->pluck('student_id')
                ->unique()
                ->count();
        }

        $classIds = $assigned->pluck('class_id')->filter()->unique()->values()->all();
        $sectionIds = $assigned->pluck('section_id')->filter()->unique()->values()->all();
        $courseIds = $assigned->pluck('course_id')->filter()->unique()->values()->all();

        $assignmentCount = Assignment::where('teacher_id', $teacherId)
            ->where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->count();

        $examCount = 0;
        $markCount = 0;
        $finalMarkCount = 0;
        $attendanceTodayCount = 0;

        if (!empty($classIds) && !empty($courseIds)) {
            $examCount = Exam::where('school_id', $schoolId)
                ->whereIn('class_id', $classIds)
                ->whereIn('course_id', $courseIds)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->count();
        }

        if (!empty($classIds) && !empty($sectionIds) && !empty($courseIds)) {
            $markCount = Mark::where('school_id', $schoolId)
                ->whereIn('class_id', $classIds)
                ->whereIn('section_id', $sectionIds)
                ->whereIn('course_id', $courseIds)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->count();

            $finalMarkCount = FinalMark::where('school_id', $schoolId)
                ->whereIn('class_id', $classIds)
                ->whereIn('section_id', $sectionIds)
                ->whereIn('course_id', $courseIds)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->count();

            $attendanceTodayCount = Attendance::where('school_id', $schoolId)
                ->whereIn('class_id', $classIds)
                ->whereIn('section_id', $sectionIds)
                ->whereIn('course_id', $courseIds)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->whereDate('created_at', now()->toDateString())
                ->count();
        }

        $recentAssignments = Assignment::with(['schoolClass', 'section', 'course'])
            ->where('teacher_id', $teacherId)
            ->where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('dashboards.teacher', [
            'assigned' => $assigned,
            'classCount' => $classCount,
            'courseCount' => $courseCount,
            'studentCount' => $studentCount,
            'assignmentCount' => $assignmentCount,
            'examCount' => $examCount,
            'markCount' => $markCount,
            'finalMarkCount' => $finalMarkCount,
            'attendanceTodayCount' => $attendanceTodayCount,
            'recentAssignments' => $recentAssignments,
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

        $parentInfo = StudentParentInfo::where('student_id', $studentId)
            ->where('school_id', $schoolId)
            ->first();

        $parentCount = 0;

        if ($parentInfo) {
            if (!empty($parentInfo->father_phone)) {
                $parentCount++;
            }

            if (!empty($parentInfo->mother_phone) && $parentInfo->mother_phone !== $parentInfo->father_phone) {
                $parentCount++;
            }
        }

        $assignmentCount = 0;

        if ($promotion) {
            $assignmentCount = Assignment::where('school_id', $schoolId)
                ->where('class_id', $promotion->class_id)
                ->where('section_id', $promotion->section_id)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->count();
        }

        $noticeCount = Notice::where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->count();

        $eventCount = Event::where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->count();

        $recentAssignments = collect();

        if ($promotion) {
            $recentAssignments = Assignment::with(['course'])
                ->where('school_id', $schoolId)
                ->where('class_id', $promotion->class_id)
                ->where('section_id', $promotion->section_id)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->latest()
                ->take(5)
                ->get();
        }

        $recentNotices = Notice::where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->latest()
            ->take(5)
            ->get();

        $upcomingEvents = Event::where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->orderBy('start', 'asc')
            ->take(5)
            ->get();

        return view('dashboards.student', [
            'promotion' => $promotion,
            'courseCount' => $courseCount,
            'teacherCount' => $teacherCount,
            'resultCount' => $resultCount,
            'parentCount' => $parentCount,
            'assignmentCount' => $assignmentCount,
            'noticeCount' => $noticeCount,
            'eventCount' => $eventCount,
            'recentAssignments' => $recentAssignments,
            'recentNotices' => $recentNotices,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    public function parent()
    {
        $user = Auth::user();
        $schoolId = $user->school_id;
        $currentSessionId = $this->currentSessionId();

        $children = StudentParentInfo::with('student')
            ->where('school_id', $schoolId)
            ->where(function ($query) use ($user) {
                $query->where('father_phone', $user->phone)
                    ->orWhere('mother_phone', $user->phone);
            })
            ->get();

        $childrenSummary = $children->map(function ($child) use ($schoolId, $currentSessionId) {
            $student = $child->student;

            if (!$student) {
                return [
                    'student' => null,
                    'className' => null,
                    'sectionName' => null,
                    'resultCount' => 0,
                    'assignmentCount' => 0,
                    'teacherCount' => 0,
                ];
            }

            $promotion = Promotion::with(['schoolClass', 'section'])
                ->where('student_id', $student->id)
                ->where('school_id', $schoolId)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->latest()
                ->first();

            $resultCount = FinalMark::where('student_id', $student->id)
                ->where('school_id', $schoolId)
                ->when($currentSessionId, function ($query, $sessionId) {
                    return $query->where('session_id', $sessionId);
                })
                ->count();

            $assignmentCount = 0;
            $teacherCount = 0;

            if ($promotion) {
                $assignmentCount = Assignment::where('school_id', $schoolId)
                    ->where('class_id', $promotion->class_id)
                    ->where('section_id', $promotion->section_id)
                    ->when($currentSessionId, function ($query, $sessionId) {
                        return $query->where('session_id', $sessionId);
                    })
                    ->count();

                $teacherCount = AssignedTeacher::where('school_id', $schoolId)
                    ->where('class_id', $promotion->class_id)
                    ->where('section_id', $promotion->section_id)
                    ->when($currentSessionId, function ($query, $sessionId) {
                        return $query->where('session_id', $sessionId);
                    })
                    ->pluck('teacher_id')
                    ->unique()
                    ->count();
            }

            return [
                'student' => $student,
                'className' => optional(optional($promotion)->schoolClass)->class_name,
                'sectionName' => optional(optional($promotion)->section)->section_name,
                'resultCount' => $resultCount,
                'assignmentCount' => $assignmentCount,
                'teacherCount' => $teacherCount,
            ];
        });

        $recentNotices = Notice::where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->latest()
            ->take(5)
            ->get();

        $upcomingEvents = Event::where('school_id', $schoolId)
            ->when($currentSessionId, function ($query, $sessionId) {
                return $query->where('session_id', $sessionId);
            })
            ->orderBy('start', 'asc')
            ->take(5)
            ->get();

        return view('dashboards.parent', [
            'childrenSummary' => $childrenSummary,
            'recentNotices' => $recentNotices,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    public function affiliate()
    {
        return view('dashboards.affiliate', [
            'referredSchoolCount' => 0,
            'pendingPayoutCount' => 0,
            'trainingProgress' => 0,
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
