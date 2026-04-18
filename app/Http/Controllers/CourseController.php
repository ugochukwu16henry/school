<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Traits\SchoolSession;
use App\Interfaces\CourseInterface;
use App\Http\Requests\CourseStoreRequest;
use App\Interfaces\SchoolSessionInterface;
use App\Repositories\PromotionRepository;

class CourseController extends Controller
{
    use SchoolSession;
    protected $schoolCourseRepository;
    protected $schoolSessionRepository;

    /**
    * Create a new Controller instance
    * 
    * @param CourseInterface $schoolCourseRepository
    * @return void
    */
    public function __construct(SchoolSessionInterface $schoolSessionRepository, CourseInterface $schoolCourseRepository) {
        $this->schoolSessionRepository = $schoolSessionRepository;
        $this->schoolCourseRepository = $schoolCourseRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CourseStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CourseStoreRequest $request)
    {
        try {
            $payload = $request->validated();
            $payload['school_id'] = auth()->user()->school_id;

            $this->schoolCourseRepository->create($payload);

            return back()->with('status', 'Course creation was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStudentCourses($student_id) {
        $loggedInUser = auth()->user();
        $student = \App\Models\User::find($student_id);

        if (!$student) {
            return abort(404);
        }

        if ($loggedInUser->role !== 'super_admin' && $student->school_id !== $loggedInUser->school_id) {
            return abort(403, 'You cannot access student courses across schools.');
        }

        $current_school_session_id = $this->getSchoolCurrentSession();
        $promotionRepository = new PromotionRepository();
        $class_info = $promotionRepository->getPromotionInfoById($current_school_session_id, $student_id);

        if (!$class_info || ($loggedInUser->role !== 'super_admin' && (int) $class_info->school_id !== (int) $loggedInUser->school_id)) {
            return abort(404);
        }

        $courses = $this->schoolCourseRepository->getByClassId($class_info->class_id);

        $data = [
            'class_info'    => $class_info,
            'courses'       => $courses,
        ];
        return view('courses.student', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $course_id
     * @return \Illuminate\Http\Response
     */
    public function edit($course_id)
    {
        $current_school_session_id = $this->getSchoolCurrentSession();
        $loggedInUser = auth()->user();

        $course = $this->schoolCourseRepository->findById($course_id);

        if (!$course) {
            return abort(404);
        }

        if ($loggedInUser->role !== 'super_admin' && (int) $course->school_id !== (int) $loggedInUser->school_id) {
            return abort(403, 'You cannot edit courses across schools.');
        }

        $data = [
            'current_school_session_id' => $current_school_session_id,
            'course'                    => $course,
            'course_id'                 => $course_id,
        ];

        return view('courses.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $loggedInUser = auth()->user();
            $courseId = $request->course_id ?? $request->id;

            if ($courseId) {
                $course = Course::find($courseId);

                if (!$course) {
                    return abort(404);
                }

                if ($loggedInUser->role !== 'super_admin' && (int) $course->school_id !== (int) $loggedInUser->school_id) {
                    return abort(403, 'You cannot update courses across schools.');
                }
            }

            $this->schoolCourseRepository->update($request);

            return back()->with('status', 'Course update was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        //
    }
}
