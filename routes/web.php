<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolSignupController;
use App\Http\Controllers\SchoolSetupController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ExamRuleController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\GradeRuleController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\GradingSystemController;
use App\Http\Controllers\SchoolSessionController;
use App\Http\Controllers\ParentClaimController;
use App\Http\Controllers\AcademicSettingController;
use App\Http\Controllers\AssignedTeacherController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UpdatePasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/school/signup', [SchoolSignupController::class, 'create'])->name('school.signup.create');
Route::post('/school/signup', [SchoolSignupController::class, 'store'])->name('school.signup.store');
Route::post('/billing/webhook/stripe', [BillingController::class, 'stripeWebhook'])->name('billing.webhook.stripe');
Route::post('/billing/webhook/paystack', [BillingController::class, 'paystackWebhook'])->name('billing.webhook.paystack');

/*
|--------------------------------------------------------------------------
| Authentication routes (explicit — works without laravel/ui at runtime)
|--------------------------------------------------------------------------
*/
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

Route::middleware('guest')->group(function () {
    Route::get('/parent/claim/{code}', [ParentClaimController::class, 'show'])->name('parent.claim.show');
    Route::post('/parent/claim/{code}', [ParentClaimController::class, 'store'])->name('parent.claim.store');
});

Route::middleware(['auth', 'school.access'])->group(function () {

    Route::get('/school/setup', [SchoolSetupController::class, 'show'])->middleware('role:admin')->name('school.setup.show');
    Route::post('/school/setup', [SchoolSetupController::class, 'store'])->middleware('role:admin')->name('school.setup.store');
    Route::get('/billing/setup', [BillingController::class, 'setup'])->middleware('role:admin')->name('billing.setup.show');
    Route::post('/billing/setup/checkout', [BillingController::class, 'startCheckout'])->middleware('role:admin')->name('billing.setup.checkout');

    Route::middleware('subscription.active')->group(function () {

    Route::prefix('school')->name('school.')->middleware('role:admin')->group(function () {
        Route::post('session/create', [SchoolSessionController::class, 'store'])->name('session.store');
        Route::post('session/browse', [SchoolSessionController::class, 'browse'])->name('session.browse');

        Route::post('semester/create', [SemesterController::class, 'store'])->name('semester.create');
        Route::post('final-marks-submission-status/update', [AcademicSettingController::class, 'updateFinalMarksSubmissionStatus'])->name('final.marks.submission.status.update');

        Route::post('attendance/type/update', [AcademicSettingController::class, 'updateAttendanceType'])->name('attendance.type.update');

        // Class
        Route::post('class/create', [SchoolClassController::class, 'store'])->name('class.create');
        Route::post('class/update', [SchoolClassController::class, 'update'])->name('class.update');

        // Sections
        Route::post('section/create', [SectionController::class, 'store'])->name('section.create');
        Route::post('section/update', [SectionController::class, 'update'])->name('section.update');

        // Courses
        Route::post('course/create', [CourseController::class, 'store'])->name('course.create');
        Route::post('course/update', [CourseController::class, 'update'])->name('course.update');

        // Teacher
        Route::post('teacher/create', [UserController::class, 'storeTeacher'])->name('teacher.create');
        Route::post('teacher/update', [UserController::class, 'updateTeacher'])->name('teacher.update');
        Route::post('teacher/assign', [AssignedTeacherController::class, 'store'])->name('teacher.assign');

        // Student
        Route::post('student/create', [UserController::class, 'storeStudent'])->name('student.create');
        Route::post('student/update', [UserController::class, 'updateStudent'])->name('student.update');
    });


    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    Route::prefix('school')->name('school.')->middleware('role:admin')->group(function () {
        Route::get('/overview', [HomeController::class, 'overview'])->name('overview');
        Route::get('/people', [HomeController::class, 'people'])->name('people');
        Route::get('/operations', [HomeController::class, 'operations'])->name('operations');
    });

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/admin', [HomeController::class, 'index'])->middleware('role:admin')->name('admin');
        Route::get('/super-admin', [DashboardController::class, 'superAdmin'])->middleware('role:super_admin')->name('super-admin');
        Route::get('/teacher', [DashboardController::class, 'teacher'])->middleware('role:teacher')->name('teacher');
        Route::get('/student', [DashboardController::class, 'student'])->middleware('role:student')->name('student');
        Route::get('/parent', [DashboardController::class, 'parent'])->middleware('role:parent')->name('parent');
        Route::get('/affiliate', [DashboardController::class, 'affiliate'])->middleware('role:affiliate')->name('affiliate');
    });

    Route::prefix('dashboard/super-admin')->name('dashboard.super-admin.')->middleware('role:super_admin')->group(function () {
        Route::get('/schools', [DashboardController::class, 'superAdminSchools'])->name('schools');
        Route::get('/revenue', [DashboardController::class, 'superAdminRevenue'])->name('revenue');
    });

    // Attendance
    Route::get('/attendances', [AttendanceController::class, 'index'])->middleware('role:admin,teacher')->name('attendance.index');
    Route::get('/attendances/view', [AttendanceController::class, 'show'])->middleware('role:admin,teacher')->name('attendance.list.show');
    Route::get('/attendances/take', [AttendanceController::class, 'create'])->middleware('role:admin,teacher')->name('attendance.create.show');
    Route::post('/attendances', [AttendanceController::class, 'store'])->middleware('role:admin,teacher')->name('attendances.store');

    // Classes and sections
    Route::get('/classes', [SchoolClassController::class, 'index'])->middleware('role:admin,teacher');
    Route::get('/class/edit/{id}', [SchoolClassController::class, 'edit'])->middleware('role:admin')->name('class.edit');
    Route::get('/sections', [SectionController::class, 'getByClassId'])->middleware('role:admin,teacher')->name('get.sections.courses.by.classId');
    Route::get('/section/edit/{id}', [SectionController::class, 'edit'])->middleware('role:admin')->name('section.edit');

    // Teachers
    Route::get('/teachers/add', function () {
        return view('teachers.add');
    })->middleware('role:admin')->name('teacher.create.show');
    Route::get('/teachers/edit/{id}', [UserController::class, 'editTeacher'])->middleware('role:admin')->name('teacher.edit.show');
    Route::get('/teachers/view/list', [UserController::class, 'getTeacherList'])->middleware('role:admin,teacher')->name('teacher.list.show');
    Route::get('/teachers/view/profile/{id}', [UserController::class, 'showTeacherProfile'])->middleware('role:admin,teacher')->name('teacher.profile.show');

    //Students
    Route::get('/students/add', [UserController::class, 'createStudent'])->middleware('role:admin')->name('student.create.show');
    Route::get('/students/edit/{id}', [UserController::class, 'editStudent'])->middleware('role:admin')->name('student.edit.show');
    Route::get('/students/view/list', [UserController::class, 'getStudentList'])->middleware('role:admin,teacher')->name('student.list.show');
    Route::get('/students/view/profile/{id}', [UserController::class, 'showStudentProfile'])->middleware('role:admin,teacher,student')->name('student.profile.show');
    Route::get('/students/view/attendance/{id}', [AttendanceController::class, 'showStudentAttendance'])->middleware('role:admin,teacher,student')->name('student.attendance.show');

    // Marks
    Route::get('/marks/create', [MarkController::class, 'create'])->middleware('role:admin,teacher')->name('course.mark.create');
    Route::post('/marks/store', [MarkController::class, 'store'])->middleware('role:admin,teacher')->name('course.mark.store');
    Route::get('/marks/results', [MarkController::class, 'index'])->middleware('role:admin,teacher')->name('course.mark.list.show');
    // Route::get('/marks/view', function () {
    //     return view('marks.view');
    // });
    Route::get('/marks/view', [MarkController::class, 'showCourseMark'])->middleware('role:admin,teacher,student')->name('course.mark.show');
    Route::get('/marks/final/submit', [MarkController::class, 'showFinalMark'])->middleware('role:admin,teacher')->name('course.final.mark.submit.show');
    Route::post('/marks/final/submit', [MarkController::class, 'storeFinalMark'])->middleware('role:admin,teacher')->name('course.final.mark.submit.store');

    // Exams
    Route::get('/exams/view', [ExamController::class, 'index'])->middleware('role:admin,teacher')->name('exam.list.show');
    // Route::get('/exams/view/history', function () {
    //     return view('exams.history');
    // });
    Route::post('/exams/create', [ExamController::class, 'store'])->middleware('role:admin,teacher')->name('exam.create');
    // Route::post('/exams/delete', [ExamController::class, 'delete'])->name('exam.delete');
    Route::get('/exams/create', [ExamController::class, 'create'])->middleware('role:admin,teacher')->name('exam.create.show');
    Route::get('/exams/add-rule', [ExamRuleController::class, 'create'])->middleware('role:admin')->name('exam.rule.create');
    Route::post('/exams/add-rule', [ExamRuleController::class, 'store'])->middleware('role:admin')->name('exam.rule.store');
    Route::get('/exams/edit-rule', [ExamRuleController::class, 'edit'])->middleware('role:admin')->name('exam.rule.edit');
    Route::post('/exams/edit-rule', [ExamRuleController::class, 'update'])->middleware('role:admin')->name('exam.rule.update');
    Route::get('/exams/view-rule', [ExamRuleController::class, 'index'])->middleware('role:admin,teacher')->name('exam.rule.show');
    Route::get('/exams/grade/create', [GradingSystemController::class, 'create'])->middleware('role:admin')->name('exam.grade.system.create');
    Route::post('/exams/grade/create', [GradingSystemController::class, 'store'])->middleware('role:admin')->name('exam.grade.system.store');
    Route::get('/exams/grade/view', [GradingSystemController::class, 'index'])->middleware('role:admin,teacher')->name('exam.grade.system.index');
    Route::get('/exams/grade/add-rule', [GradeRuleController::class, 'create'])->middleware('role:admin')->name('exam.grade.system.rule.create');
    Route::post('/exams/grade/add-rule', [GradeRuleController::class, 'store'])->middleware('role:admin')->name('exam.grade.system.rule.store');
    Route::get('/exams/grade/view-rules', [GradeRuleController::class, 'index'])->middleware('role:admin,teacher')->name('exam.grade.system.rule.show');
    Route::post('/exams/grade/delete-rule', [GradeRuleController::class, 'destroy'])->middleware('role:admin')->name('exam.grade.system.rule.delete');

    // Promotions
    Route::get('/promotions/index', [PromotionController::class, 'index'])->middleware('role:admin')->name('promotions.index');
    Route::get('/promotions/promote', [PromotionController::class, 'create'])->middleware('role:admin')->name('promotions.create');
    Route::post('/promotions/promote', [PromotionController::class, 'store'])->middleware('role:admin')->name('promotions.store');

    // Academic settings
    Route::get('/academics/settings', [AcademicSettingController::class, 'index'])->middleware('role:admin');

    // Calendar events
    Route::get('calendar-event', [EventController::class, 'index'])->middleware('role:admin')->name('events.show');
    Route::post('calendar-crud-ajax', [EventController::class, 'calendarEvents'])->middleware('role:admin')->name('events.crud');

    // Routines
    Route::get('/routine/create', [RoutineController::class, 'create'])->middleware('role:admin')->name('section.routine.create');
    Route::get('/routine/view', [RoutineController::class, 'show'])->middleware('role:admin,teacher,student')->name('section.routine.show');
    Route::post('/routine/store', [RoutineController::class, 'store'])->middleware('role:admin')->name('section.routine.store');

    // Syllabus
    Route::get('/syllabus/create', [SyllabusController::class, 'create'])->middleware('role:admin,teacher')->name('class.syllabus.create');
    Route::post('/syllabus/create', [SyllabusController::class, 'store'])->middleware('role:admin,teacher')->name('syllabus.store');
    Route::get('/syllabus/index', [SyllabusController::class, 'index'])->middleware('role:admin,teacher,student')->name('course.syllabus.index');

    // Notices
    Route::get('/notice/create', [NoticeController::class, 'create'])->middleware('role:admin')->name('notice.create');
    Route::post('/notice/create', [NoticeController::class, 'store'])->middleware('role:admin')->name('notice.store');

    // Courses
    Route::get('courses/teacher/index', [AssignedTeacherController::class, 'getTeacherCourses'])->middleware('role:admin,teacher')->name('course.teacher.list.show');
    Route::get('courses/student/index/{student_id}', [CourseController::class, 'getStudentCourses'])->middleware('role:admin,teacher,student')->name('course.student.list.show');
    Route::get('course/edit/{id}', [CourseController::class, 'edit'])->middleware('role:admin')->name('course.edit');

    // Assignment
    Route::get('courses/assignments/index', [AssignmentController::class, 'getCourseAssignments'])->middleware('role:admin,teacher,student')->name('assignment.list.show');
    Route::get('courses/assignments/create', [AssignmentController::class, 'create'])->middleware('role:admin,teacher')->name('assignment.create');
    Route::post('courses/assignments/create', [AssignmentController::class, 'store'])->middleware('role:admin,teacher')->name('assignment.store');

    // Update password
    Route::get('password/edit', [UpdatePasswordController::class, 'edit'])->name('password.edit');
    Route::post('password/edit', [UpdatePasswordController::class, 'update'])->name('user.password.update');
    });
});
