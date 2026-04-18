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

class TenantIsolationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_edit_course_from_another_school()
    {
        $this->withoutMiddleware();

        $schoolA = School::create([
            'name' => 'School A',
            'slug' => 'school-a',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $schoolB = School::create([
            'name' => 'School B',
            'slug' => 'school-b',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $sessionB = SchoolSession::create([
            'session_name' => '2026/2027',
            'school_id' => $schoolB->id,
        ]);

        $semesterB = Semester::create([
            'semester_name' => 'First',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
            'session_id' => $sessionB->id,
            'school_id' => $schoolB->id,
        ]);

        $classB = SchoolClass::create([
            'class_name' => 'JSS 1',
            'session_id' => $sessionB->id,
            'school_id' => $schoolB->id,
        ]);

        $foreignCourse = Course::create([
            'course_name' => 'Mathematics',
            'course_type' => 'Core',
            'class_id' => $classB->id,
            'semester_id' => $semesterB->id,
            'session_id' => $sessionB->id,
            'school_id' => $schoolB->id,
        ]);

        $adminA = User::factory()->create([
            'role' => 'admin',
            'school_id' => $schoolA->id,
        ]);
        /** @var User $adminA */

        $response = $this->actingAs($adminA)->get('/course/edit/' . $foreignCourse->id);

        $response->assertStatus(403);
    }
}
