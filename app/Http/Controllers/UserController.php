<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\SchoolSession;
use App\Interfaces\UserInterface;
use App\Interfaces\SectionInterface;
use App\Interfaces\SchoolClassInterface;
use App\Repositories\PromotionRepository;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\TeacherStoreRequest;
use App\Interfaces\SchoolSessionInterface;
use App\Repositories\StudentParentInfoRepository;

class UserController extends Controller
{
    use SchoolSession;
    protected $userRepository;
    protected $schoolSessionRepository;
    protected $schoolClassRepository;
    protected $schoolSectionRepository;

    public function __construct(UserInterface $userRepository, SchoolSessionInterface $schoolSessionRepository,
    SchoolClassInterface $schoolClassRepository,
    SectionInterface $schoolSectionRepository)
    {
        $this->middleware(['can:view users']);

        $this->userRepository = $userRepository;
        $this->schoolSessionRepository = $schoolSessionRepository;
        $this->schoolClassRepository = $schoolClassRepository;
        $this->schoolSectionRepository = $schoolSectionRepository;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  TeacherStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeTeacher(TeacherStoreRequest $request)
    {
        try {
            $payload = $request->validated();
            $payload['school_id'] = auth()->user()->school_id;

            $this->userRepository->createTeacher($payload);

            return back()->with('status', 'Teacher creation was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function getStudentList(Request $request) {
        $current_school_session_id = $this->getSchoolCurrentSession();

        $class_id = $request->query('class_id', 0);
        $section_id = $request->query('section_id', 0);

        try{

            $school_classes = $this->schoolClassRepository->getAllBySession($current_school_session_id);

            $studentList = $this->userRepository->getAllStudents($current_school_session_id, $class_id, $section_id);

            $data = [
                'studentList'       => $studentList,
                'school_classes'    => $school_classes,
            ];

            return view('students.list', $data);
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }


    public function showStudentProfile($id) {
        $loggedInUser = auth()->user();
        $student = $this->userRepository->findStudent($id, $loggedInUser->role === 'super_admin' ? null : $loggedInUser->school_id);

        if (!$student) {
            return abort(404);
        }

        $current_school_session_id = $this->getSchoolCurrentSession();
        $promotionRepository = new PromotionRepository();
        $promotion_info = $promotionRepository->getPromotionInfoById($current_school_session_id, $id);

        $data = [
            'student'           => $student,
            'promotion_info'    => $promotion_info,
        ];

        return view('students.profile', $data);
    }

    public function showTeacherProfile($id) {
        $loggedInUser = auth()->user();
        $teacher = $this->userRepository->findTeacher($id, $loggedInUser->role === 'super_admin' ? null : $loggedInUser->school_id);

        if (!$teacher) {
            return abort(404);
        }

        $data = [
            'teacher'   => $teacher,
        ];
        return view('teachers.profile', $data);
    }


    public function createStudent() {
        $current_school_session_id = $this->getSchoolCurrentSession();

        $school_classes = $this->schoolClassRepository->getAllBySession($current_school_session_id);

        $data = [
            'current_school_session_id' => $current_school_session_id,
            'school_classes'            => $school_classes,
        ];

        return view('students.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StudentStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function storeStudent(StudentStoreRequest $request)
    {
        try {
            $payload = $request->validated();
            $payload['school_id'] = auth()->user()->school_id;

            $this->userRepository->createStudent($payload);

            return back()->with('status', 'Student creation was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function editStudent($student_id) {
        $loggedInUser = auth()->user();
        $student = $this->userRepository->findStudent($student_id, $loggedInUser->role === 'super_admin' ? null : $loggedInUser->school_id);

        if (!$student) {
            return abort(404);
        }

        $studentParentInfoRepository = new StudentParentInfoRepository();
        $parent_info = $studentParentInfoRepository->getParentInfo($student_id, $loggedInUser->role === 'super_admin' ? null : $loggedInUser->school_id);
        $promotionRepository = new PromotionRepository();
        $current_school_session_id = $this->getSchoolCurrentSession();
        $promotion_info = $promotionRepository->getPromotionInfoById($current_school_session_id, $student_id);

        $data = [
            'student'       => $student,
            'parent_info'   => $parent_info,
            'promotion_info'=> $promotion_info,
        ];
        return view('students.edit', $data);
    }

    public function updateStudent(Request $request) {
        try {
            $loggedInUser = auth()->user();
            $student = User::find($request->student_id);

            if (!$student) {
                return abort(404);
            }

            if ($loggedInUser->role !== 'super_admin' && (int) $student->school_id !== (int) $loggedInUser->school_id) {
                return abort(403, 'You cannot update students across schools.');
            }

            $payload = $request->toArray();
            $payload['school_id'] = $loggedInUser->role === 'super_admin' ? null : $loggedInUser->school_id;

            $this->userRepository->updateStudent($payload);

            return back()->with('status', 'Student update was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function editTeacher($teacher_id) {
        $loggedInUser = auth()->user();
        $teacher = $this->userRepository->findTeacher($teacher_id, $loggedInUser->role === 'super_admin' ? null : $loggedInUser->school_id);

        if (!$teacher) {
            return abort(404);
        }

        $data = [
            'teacher'   => $teacher,
        ];

        return view('teachers.edit', $data);
    }
    public function updateTeacher(Request $request) {
        try {
            $loggedInUser = auth()->user();
            $teacher = User::where('id', $request->teacher_id)->where('role', 'teacher')->first();

            if (!$teacher) {
                return abort(404);
            }

            if ($loggedInUser->role !== 'super_admin' && (int) $teacher->school_id !== (int) $loggedInUser->school_id) {
                return abort(403, 'You cannot update teachers across schools.');
            }

            $this->userRepository->updateTeacher($request->toArray());

            return back()->with('status', 'Teacher update was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function getTeacherList(){
        $loggedInUser = auth()->user();
        $teachers = $this->userRepository->getAllTeachers($loggedInUser->role === 'super_admin' ? null : $loggedInUser->school_id);

        $data = [
            'teachers' => $teachers,
        ];

        return view('teachers.list', $data);
    }
}
