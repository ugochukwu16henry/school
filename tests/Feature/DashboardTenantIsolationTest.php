<?php

namespace Tests\Feature;

use App\Models\AssignedTeacher;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Event;
use App\Models\Notice;
use App\Models\Promotion;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolSession;
use App\Models\Section;
use App\Models\Semester;
use App\Models\StudentParentInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_dashboard_shows_only_own_school_children_notices_and_events()
    {
        $schoolA = $this->createSchool('parent-a');
        $schoolB = $this->createSchool('parent-b');

        $sessionA = $this->createSession($schoolA->id, '2026/2027-A');
        $sessionB = $this->createSession($schoolB->id, '2026/2027-B');

        $parentA = $this->createUser('parent', $schoolA->id, '08000000001');
        $studentA = $this->createUser('student', $schoolA->id, '08000000011');
        $studentB = $this->createUser('student', $schoolB->id, '08000000022');

        StudentParentInfo::create([
            'student_id' => $studentA->id,
            'father_name' => 'Parent A',
            'father_phone' => $parentA->phone,
            'mother_name' => 'Parent A2',
            'mother_phone' => '08000000999',
            'parent_address' => 'Address A',
            'school_id' => $schoolA->id,
        ]);

        StudentParentInfo::create([
            'student_id' => $studentB->id,
            'father_name' => 'Foreign Parent',
            'father_phone' => $parentA->phone,
            'mother_name' => 'Foreign Parent 2',
            'mother_phone' => '08000000888',
            'parent_address' => 'Address B',
            'school_id' => $schoolB->id,
        ]);

        Notice::create([
            'notice' => 'Notice for school A only',
            'session_id' => $sessionA->id,
            'school_id' => $schoolA->id,
        ]);

        Notice::create([
            'notice' => 'Notice for school B only',
            'session_id' => $sessionB->id,
            'school_id' => $schoolB->id,
        ]);

        Event::create([
            'title' => 'Event for school A only',
            'start' => now()->toDateTimeString(),
            'end' => now()->addHour()->toDateTimeString(),
            'session_id' => $sessionA->id,
            'school_id' => $schoolA->id,
        ]);

        Event::create([
            'title' => 'Event for school B only',
            'start' => now()->toDateTimeString(),
            'end' => now()->addHour()->toDateTimeString(),
            'session_id' => $sessionB->id,
            'school_id' => $schoolB->id,
        ]);

        $response = $this->actingAs($parentA)->get('/dashboard/parent');

        $response->assertStatus(200);
        $response->assertSee($studentA->first_name);
        $response->assertDontSee($studentB->first_name);
        $response->assertSee('Notice for school A only');
        $response->assertDontSee('Notice for school B only');
        $response->assertSee('Event for school A only');
        $response->assertDontSee('Event for school B only');
    }

    public function test_student_dashboard_shows_only_own_school_assignments_notices_and_events()
    {
        $schoolA = $this->createSchool('student-a');
        $schoolB = $this->createSchool('student-b');

        $contextA = $this->createAcademicContext($schoolA->id, '2027/2028-A');
        $contextB = $this->createAcademicContext($schoolB->id, '2027/2028-B');

        $studentA = $this->createUser('student', $schoolA->id, '08000000111');

        Promotion::create([
            'student_id' => $studentA->id,
            'class_id' => $contextA['class']->id,
            'section_id' => $contextA['section']->id,
            'session_id' => $contextA['session']->id,
            'id_card_number' => 'ST-A-1',
            'school_id' => $schoolA->id,
        ]);

        Assignment::create([
            'teacher_id' => $this->createUser('teacher', $schoolA->id, '08000000121')->id,
            'semester_id' => $contextA['semester']->id,
            'class_id' => $contextA['class']->id,
            'section_id' => $contextA['section']->id,
            'course_id' => $contextA['course']->id,
            'session_id' => $contextA['session']->id,
            'assignment_name' => 'Algebra Homework A',
            'assignment_file_path' => 'assignments/a.pdf',
            'school_id' => $schoolA->id,
        ]);

        Assignment::create([
            'teacher_id' => $this->createUser('teacher', $schoolB->id, '08000000122')->id,
            'semester_id' => $contextB['semester']->id,
            'class_id' => $contextB['class']->id,
            'section_id' => $contextB['section']->id,
            'course_id' => $contextB['course']->id,
            'session_id' => $contextB['session']->id,
            'assignment_name' => 'Foreign Homework B',
            'assignment_file_path' => 'assignments/b.pdf',
            'school_id' => $schoolB->id,
        ]);

        Notice::create([
            'notice' => 'Student Notice A',
            'session_id' => $contextA['session']->id,
            'school_id' => $schoolA->id,
        ]);

        Notice::create([
            'notice' => 'Student Notice B',
            'session_id' => $contextB['session']->id,
            'school_id' => $schoolB->id,
        ]);

        Event::create([
            'title' => 'Student Event A',
            'start' => now()->toDateTimeString(),
            'end' => now()->addHour()->toDateTimeString(),
            'session_id' => $contextA['session']->id,
            'school_id' => $schoolA->id,
        ]);

        Event::create([
            'title' => 'Student Event B',
            'start' => now()->toDateTimeString(),
            'end' => now()->addHour()->toDateTimeString(),
            'session_id' => $contextB['session']->id,
            'school_id' => $schoolB->id,
        ]);

        $response = $this->actingAs($studentA)->get('/dashboard/student');

        $response->assertStatus(200);
        $response->assertSee('Algebra Homework A');
        $response->assertDontSee('Foreign Homework B');
        $response->assertSee('Student Notice A');
        $response->assertDontSee('Student Notice B');
        $response->assertSee('Student Event A');
        $response->assertDontSee('Student Event B');
    }

    public function test_teacher_dashboard_shows_only_own_school_recent_assignments()
    {
        $schoolA = $this->createSchool('teacher-a');
        $schoolB = $this->createSchool('teacher-b');

        $contextA = $this->createAcademicContext($schoolA->id, '2028/2029-A');
        $contextB = $this->createAcademicContext($schoolB->id, '2028/2029-B');

        $contextA['course']->update(['course_name' => 'Teacher A Course']);
        $contextB['course']->update(['course_name' => 'Teacher B Course']);

        $teacherA = $this->createUser('teacher', $schoolA->id, '08000000221');
        $teacherB = $this->createUser('teacher', $schoolB->id, '08000000222');

        AssignedTeacher::create([
            'teacher_id' => $teacherA->id,
            'semester_id' => $contextA['semester']->id,
            'class_id' => $contextA['class']->id,
            'section_id' => $contextA['section']->id,
            'course_id' => $contextA['course']->id,
            'session_id' => $contextA['session']->id,
            'school_id' => $schoolA->id,
        ]);

        Assignment::create([
            'teacher_id' => $teacherA->id,
            'semester_id' => $contextA['semester']->id,
            'class_id' => $contextA['class']->id,
            'section_id' => $contextA['section']->id,
            'course_id' => $contextA['course']->id,
            'session_id' => $contextA['session']->id,
            'assignment_name' => 'Teacher A Assignment',
            'assignment_file_path' => 'assignments/a-teacher.pdf',
            'school_id' => $schoolA->id,
        ]);

        AssignedTeacher::create([
            'teacher_id' => $teacherB->id,
            'semester_id' => $contextB['semester']->id,
            'class_id' => $contextB['class']->id,
            'section_id' => $contextB['section']->id,
            'course_id' => $contextB['course']->id,
            'session_id' => $contextB['session']->id,
            'school_id' => $schoolB->id,
        ]);

        Assignment::create([
            'teacher_id' => $teacherB->id,
            'semester_id' => $contextB['semester']->id,
            'class_id' => $contextB['class']->id,
            'section_id' => $contextB['section']->id,
            'course_id' => $contextB['course']->id,
            'session_id' => $contextB['session']->id,
            'assignment_name' => 'Teacher B Assignment',
            'assignment_file_path' => 'assignments/b-teacher.pdf',
            'school_id' => $schoolB->id,
        ]);

        $response = $this->actingAs($teacherA)->get('/dashboard/teacher');

        $response->assertStatus(200);
        $response->assertSee('Teacher A Course');
        $response->assertDontSee('Teacher B Course');
    }

    private function createSchool(string $slug): School
    {
        $uniqueSlug = $slug . '-' . uniqid();

        return School::create([
            'name' => 'School ' . $uniqueSlug,
            'slug' => $uniqueSlug,
            'status' => 'active',
            'plan' => 'starter',
        ]);
    }

    private function createSession(int $schoolId, string $sessionName): SchoolSession
    {
        return SchoolSession::create([
            'session_name' => $sessionName,
            'school_id' => $schoolId,
        ]);
    }

    /**
     * @return array{session: SchoolSession, semester: Semester, class: SchoolClass, section: Section, course: Course}
     */
    private function createAcademicContext(int $schoolId, string $sessionName): array
    {
        $session = $this->createSession($schoolId, $sessionName);

        $semester = Semester::create([
            'semester_name' => 'First',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
            'session_id' => $session->id,
            'school_id' => $schoolId,
        ]);

        $class = SchoolClass::create([
            'class_name' => 'JSS ' . rand(1, 3),
            'session_id' => $session->id,
            'school_id' => $schoolId,
        ]);

        $section = Section::create([
            'section_name' => 'Section ' . rand(1, 3),
            'room_no' => 'R-' . rand(100, 999),
            'class_id' => $class->id,
            'session_id' => $session->id,
            'school_id' => $schoolId,
        ]);

        $course = Course::create([
            'course_name' => 'Course ' . uniqid(),
            'course_type' => 'Core',
            'class_id' => $class->id,
            'semester_id' => $semester->id,
            'session_id' => $session->id,
            'school_id' => $schoolId,
        ]);

        return [
            'session' => $session,
            'semester' => $semester,
            'class' => $class,
            'section' => $section,
            'course' => $course,
        ];
    }

    private function createUser(string $role, ?int $schoolId, string $phone): User
    {
        return User::factory()->create([
            'role' => $role,
            'school_id' => $schoolId,
            'phone' => $phone,
        ]);
    }
}
