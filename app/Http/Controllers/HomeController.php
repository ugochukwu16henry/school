<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\SchoolSession;
use App\Interfaces\UserInterface;
use App\Models\User;
use App\Repositories\NoticeRepository;
use App\Interfaces\SchoolClassInterface;
use App\Interfaces\SchoolSessionInterface;
use App\Repositories\PromotionRepository;

class HomeController extends Controller
{
    use SchoolSession;
    protected $schoolSessionRepository;
    protected $schoolClassRepository;
    protected $userRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserInterface $userRepository, SchoolSessionInterface $schoolSessionRepository, SchoolClassInterface $schoolClassRepository)
    {
        // $this->middleware('auth');
        $this->userRepository = $userRepository;
        $this->schoolSessionRepository = $schoolSessionRepository;
        $this->schoolClassRepository = $schoolClassRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return $this->overview();
    }

    /**
     * School admin overview dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function overview()
    {
        return view('school.overview', $this->dashboardData());
    }

    /**
     * School admin people dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function people()
    {
        return view('school.people', $this->dashboardData());
    }

    /**
     * School admin operations dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function operations()
    {
        return view('school.operations', $this->dashboardData());
    }

    /**
     * Shared dashboard data for school admin pages.
     *
     * @return array<string, mixed>
     */
    private function dashboardData()
    {
        $current_school_session_id = $this->getSchoolCurrentSession();

        $classCount = $this->schoolClassRepository->getAllBySession($current_school_session_id)->count();

        $studentCount = $this->userRepository->getAllStudentsBySessionCount($current_school_session_id);

        $promotionRepository = new PromotionRepository();

        $maleStudentsBySession = $promotionRepository->getMaleStudentsBySessionCount($current_school_session_id);

        $teacherCount = $this->userRepository->getAllTeachers()->count();
        $parentCount = User::where('role', 'parent')->count();

        $noticeRepository = new NoticeRepository();
        $notices = $noticeRepository->getAll($current_school_session_id);

        $data = [
            'classCount'    => $classCount,
            'studentCount'  => $studentCount,
            'teacherCount'  => $teacherCount,
            'parentCount'   => $parentCount,
            'notices'       => $notices,
            'maleStudentsBySession' => $maleStudentsBySession,
        ];

        return $data;
    }
}
