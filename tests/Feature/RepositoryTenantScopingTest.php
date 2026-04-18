<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolSession;
use App\Models\Section;
use App\Models\Semester;
use App\Models\User;
use App\Repositories\AttendanceRepository;
use App\Repositories\MarkRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepositoryTenantScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_repository_filters_by_school_id()
    {
        [$schoolA, $schoolB, $session, $class, $section, $course] = $this->seedCommonAcademicData();

        Attendance::create([
            'student_id' => User::factory()->create(['role' => 'student', 'school_id' => $schoolA->id])->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'session_id' => $session->id,
            'course_id' => $course->id,
            'school_id' => $schoolA->id,
            'status' => 'present',
        ]);

        Attendance::create([
            'student_id' => User::factory()->create(['role' => 'student', 'school_id' => $schoolB->id])->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'session_id' => $session->id,
            'course_id' => $course->id,
            'school_id' => $schoolB->id,
            'status' => 'present',
        ]);

        $repo = new AttendanceRepository();

        $schoolAAttendance = $repo->getSectionAttendance($class->id, $section->id, $session->id, $schoolA->id);

        $this->assertCount(1, $schoolAAttendance, 'Attendance repository returned records outside tenant scope.');
        $this->assertSame((int) $schoolA->id, (int) $schoolAAttendance->first()->school_id, 'Attendance repository returned wrong tenant school_id.');
    }

    public function test_mark_repository_filters_by_school_id()
    {
        [$schoolA, $schoolB, $session, $class, $section, $course] = $this->seedCommonAcademicData();

        $semester = Semester::create([
            'semester_name' => 'First',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
            'session_id' => $session->id,
            'school_id' => $schoolA->id,
        ]);

        $examA = Exam::create([
            'exam_name' => 'Midterm A',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'semester_id' => $semester->id,
            'class_id' => $class->id,
            'course_id' => $course->id,
            'session_id' => $session->id,
            'school_id' => $schoolA->id,
        ]);

        $examB = Exam::create([
            'exam_name' => 'Midterm B',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
            'semester_id' => $semester->id,
            'class_id' => $class->id,
            'course_id' => $course->id,
            'session_id' => $session->id,
            'school_id' => $schoolB->id,
        ]);

        Mark::create([
            'marks' => 88,
            'student_id' => User::factory()->create(['role' => 'student', 'school_id' => $schoolA->id])->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'course_id' => $course->id,
            'exam_id' => $examA->id,
            'session_id' => $session->id,
            'school_id' => $schoolA->id,
        ]);

        Mark::create([
            'marks' => 75,
            'student_id' => User::factory()->create(['role' => 'student', 'school_id' => $schoolB->id])->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'course_id' => $course->id,
            'exam_id' => $examB->id,
            'session_id' => $session->id,
            'school_id' => $schoolB->id,
        ]);

        $repo = new MarkRepository();

        $schoolAMarks = $repo->getAll(
            $session->id,
            $semester->id,
            $class->id,
            $section->id,
            $course->id,
            $schoolA->id
        );

        $this->assertCount(1, $schoolAMarks, 'Mark repository returned records outside tenant scope.');
        $this->assertSame((int) $schoolA->id, (int) $schoolAMarks->first()->school_id, 'Mark repository returned wrong tenant school_id.');
    }

    private function seedCommonAcademicData(): array
    {
        $suffix = uniqid();

        $schoolA = School::create([
            'name' => 'Repo Scope A',
            'slug' => 'repo-scope-a-' . $suffix,
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $schoolB = School::create([
            'name' => 'Repo Scope B',
            'slug' => 'repo-scope-b-' . $suffix,
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $session = SchoolSession::create([
            'session_name' => '2030/2031',
            'school_id' => $schoolA->id,
        ]);

        $class = SchoolClass::create([
            'class_name' => 'JSS 2',
            'session_id' => $session->id,
            'school_id' => $schoolA->id,
        ]);

        $section = Section::create([
            'section_name' => 'Blue',
            'room_no' => 'R-01',
            'class_id' => $class->id,
            'session_id' => $session->id,
            'school_id' => $schoolA->id,
        ]);

        $course = Course::create([
            'course_name' => 'English',
            'course_type' => 'Core',
            'class_id' => $class->id,
            'semester_id' => 1,
            'session_id' => $session->id,
            'school_id' => $schoolA->id,
        ]);

        return [$schoolA, $schoolB, $session, $class, $section, $course];
    }
}
