<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolSession;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSchoolScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_queries_are_automatically_scoped_by_school()
    {
        $schoolA = School::create([
            'name' => 'Scope School A',
            'slug' => 'scope-school-a',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $schoolB = School::create([
            'name' => 'Scope School B',
            'slug' => 'scope-school-b',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $adminA = User::factory()->create([
            'role' => 'admin',
            'school_id' => $schoolA->id,
        ]);
        /** @var User $adminA */

        User::factory()->create([
            'role' => 'teacher',
            'school_id' => $schoolA->id,
        ]);

        User::factory()->create([
            'role' => 'teacher',
            'school_id' => $schoolB->id,
        ]);

        $this->actingAs($adminA);

        $visibleTeachers = User::where('role', 'teacher')->get();

        if ($visibleTeachers->count() !== 1) {
            throw new \RuntimeException('Global school scope failed: user query returned cross-school records.');
        }

        if ((int) $visibleTeachers->first()->school_id !== (int) $schoolA->id) {
            throw new \RuntimeException('Global school scope failed: wrong school user returned.');
        }
    }

    public function test_course_queries_are_scoped_by_school_without_manual_filters()
    {
        $schoolA = School::create([
            'name' => 'Course Scope A',
            'slug' => 'course-scope-a',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $schoolB = School::create([
            'name' => 'Course Scope B',
            'slug' => 'course-scope-b',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $adminA = User::factory()->create([
            'role' => 'admin',
            'school_id' => $schoolA->id,
        ]);
        /** @var User $adminA */

        $sessionA = SchoolSession::create([
            'session_name' => '2031/2032',
            'school_id' => $schoolA->id,
        ]);

        $classA = SchoolClass::create([
            'class_name' => 'SS1',
            'session_id' => $sessionA->id,
            'school_id' => $schoolA->id,
        ]);

        $semesterA = Semester::create([
            'semester_name' => 'First',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
            'session_id' => $sessionA->id,
            'school_id' => $schoolA->id,
        ]);

        Course::create([
            'course_name' => 'Biology',
            'course_type' => 'Core',
            'class_id' => $classA->id,
            'semester_id' => $semesterA->id,
            'session_id' => $sessionA->id,
            'school_id' => $schoolA->id,
        ]);

        Course::create([
            'course_name' => 'Chemistry',
            'course_type' => 'Core',
            'class_id' => $classA->id,
            'semester_id' => $semesterA->id,
            'session_id' => $sessionA->id,
            'school_id' => $schoolB->id,
        ]);

        $this->actingAs($adminA);

        $visibleCourses = Course::query()->get();

        if ($visibleCourses->count() !== 1) {
            throw new \RuntimeException('Global school scope failed: course query returned cross-school records.');
        }

        if ((int) $visibleCourses->first()->school_id !== (int) $schoolA->id) {
            throw new \RuntimeException('Global school scope failed: wrong school course returned.');
        }
    }
}
