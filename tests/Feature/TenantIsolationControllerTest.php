<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Event;
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

    public function test_admin_cannot_edit_event_from_another_school()
    {
        $this->withoutMiddleware();

        $schoolA = School::create([
            'name' => 'School A2',
            'slug' => 'school-a2',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $schoolB = School::create([
            'name' => 'School B2',
            'slug' => 'school-b2',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $sessionB = SchoolSession::create([
            'session_name' => '2027/2028',
            'school_id' => $schoolB->id,
        ]);

        $foreignEvent = Event::create([
            'title' => 'Sports Day',
            'start' => now()->toDateTimeString(),
            'end' => now()->addHour()->toDateTimeString(),
            'session_id' => $sessionB->id,
            'school_id' => $schoolB->id,
        ]);

        $adminA = User::factory()->create([
            'role' => 'admin',
            'school_id' => $schoolA->id,
        ]);
        /** @var User $adminA */

        $response = $this->actingAs($adminA)
            ->withSession(['browse_session_id' => $sessionB->id])
            ->post('/calendar-crud-ajax', [
                'type' => 'edit',
                'id' => $foreignEvent->id,
                'title' => 'Updated',
                'start' => now()->toDateTimeString(),
                'end' => now()->addHour()->toDateTimeString(),
            ]);

        $response->assertStatus(404);
    }

    public function test_admin_cannot_view_teacher_profile_from_another_school()
    {
        $this->withoutMiddleware();

        $schoolA = School::create([
            'name' => 'School A3',
            'slug' => 'school-a3',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $schoolB = School::create([
            'name' => 'School B3',
            'slug' => 'school-b3',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $foreignTeacher = User::factory()->create([
            'role' => 'teacher',
            'school_id' => $schoolB->id,
        ]);

        $adminA = User::factory()->create([
            'role' => 'admin',
            'school_id' => $schoolA->id,
        ]);
        /** @var User $adminA */

        $response = $this->actingAs($adminA)->get('/teachers/view/profile/' . $foreignTeacher->id);

        $response->assertStatus(404);
    }

    public function test_admin_cannot_view_teacher_courses_for_another_school_teacher()
    {
        $this->withoutMiddleware();

        $schoolA = School::create([
            'name' => 'School A4',
            'slug' => 'school-a4',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $schoolB = School::create([
            'name' => 'School B4',
            'slug' => 'school-b4',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $sessionB = SchoolSession::create([
            'session_name' => '2028/2029',
            'school_id' => $schoolB->id,
        ]);

        $foreignTeacher = User::factory()->create([
            'role' => 'teacher',
            'school_id' => $schoolB->id,
        ]);

        $adminA = User::factory()->create([
            'role' => 'admin',
            'school_id' => $schoolA->id,
        ]);
        /** @var User $adminA */

        $response = $this->actingAs($adminA)
            ->withSession(['browse_session_id' => $sessionB->id])
            ->get('/courses/teacher/index?teacher_id=' . $foreignTeacher->id . '&semester_id=1');

        $response->assertStatus(404);
    }

    public function test_super_admin_can_edit_course_from_another_school()
    {
        $this->withoutMiddleware();

        $schoolA = School::create([
            'name' => 'School SA1',
            'slug' => 'school-sa1',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $sessionA = SchoolSession::create([
            'session_name' => '2029/2030',
            'school_id' => $schoolA->id,
        ]);

        $semesterA = Semester::create([
            'semester_name' => 'First',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
            'session_id' => $sessionA->id,
            'school_id' => $schoolA->id,
        ]);

        $classA = SchoolClass::create([
            'class_name' => 'SS 2',
            'session_id' => $sessionA->id,
            'school_id' => $schoolA->id,
        ]);

        $foreignCourse = Course::create([
            'course_name' => 'Physics',
            'course_type' => 'Core',
            'class_id' => $classA->id,
            'semester_id' => $semesterA->id,
            'session_id' => $sessionA->id,
            'school_id' => $schoolA->id,
        ]);

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'school_id' => null,
        ]);
        /** @var User $superAdmin */

        $response = $this->actingAs($superAdmin)->get('/course/edit/' . $foreignCourse->id);

        $response->assertStatus(200);
    }

    public function test_super_admin_can_view_teacher_profile_from_any_school()
    {
        $this->withoutMiddleware();

        $schoolA = School::create([
            'name' => 'School SA2',
            'slug' => 'school-sa2',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $foreignTeacher = User::factory()->create([
            'role' => 'teacher',
            'school_id' => $schoolA->id,
        ]);

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'school_id' => null,
        ]);
        /** @var User $superAdmin */

        $response = $this->actingAs($superAdmin)->get('/teachers/view/profile/' . $foreignTeacher->id);

        $response->assertStatus(200);
    }

    public function test_super_admin_can_edit_event_from_any_school()
    {
        $this->withoutMiddleware();

        $schoolA = School::create([
            'name' => 'School SA3',
            'slug' => 'school-sa3',
            'status' => 'active',
            'plan' => 'starter',
        ]);

        $sessionA = SchoolSession::create([
            'session_name' => '2030/2031',
            'school_id' => $schoolA->id,
        ]);

        $foreignEvent = Event::create([
            'title' => 'Cross Event',
            'start' => now()->toDateTimeString(),
            'end' => now()->addHour()->toDateTimeString(),
            'session_id' => $sessionA->id,
            'school_id' => $schoolA->id,
        ]);

        $superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'school_id' => null,
        ]);
        /** @var User $superAdmin */

        $response = $this->actingAs($superAdmin)
            ->withSession(['browse_session_id' => $sessionA->id])
            ->post('/calendar-crud-ajax', [
                'type' => 'edit',
                'id' => $foreignEvent->id,
                'title' => 'Cross Event Updated',
                'start' => now()->toDateTimeString(),
                'end' => now()->addHour()->toDateTimeString(),
            ]);

        $response->assertStatus(200);
    }
}
