<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SchoolSession;
use App\Models\SchoolSubscription;
use App\Models\StudentParentInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardContentRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_admin_overview_people_and_operations_render_expected_sections()
    {
        $school = $this->createSchool('content-admin');
        $this->createSession($school->id, '2030/2031');
        $admin = $this->createUser('admin', $school->id);

        User::factory()->create(['role' => 'teacher', 'school_id' => $school->id]);
        User::factory()->create(['role' => 'student', 'school_id' => $school->id]);

        $overview = $this->actingAs($admin)->get('/school/overview');
        $overview->assertStatus(200);
        $overview->assertSee('School Overview');
        $overview->assertSee('Total Students');
        $overview->assertSee('Quick Actions');
        $overview->assertSee('Latest Notices');

        $people = $this->actingAs($admin)->get('/school/people');
        $people->assertStatus(200);
        $people->assertSee('People');
        $people->assertSee('Students');
        $people->assertSee('Teachers');
        $people->assertSee('Open Promotions');

        $operations = $this->actingAs($admin)->get('/school/operations');
        $operations->assertStatus(200);
        $operations->assertSee('Operations');
        $operations->assertSee('Academic Settings');
        $operations->assertSee('Billing');
        $operations->assertSee('School Setup');
    }

    public function test_super_admin_schools_page_renders_seeded_school_rows()
    {
        $superAdmin = $this->createUser('super_admin', null);

        $schoolA = $this->createSchool('content-super-a');
        $schoolB = $this->createSchool('content-super-b');

        User::factory()->create(['role' => 'admin', 'school_id' => $schoolA->id]);
        User::factory()->create(['role' => 'teacher', 'school_id' => $schoolA->id]);
        User::factory()->create(['role' => 'student', 'school_id' => $schoolA->id]);

        SchoolSubscription::create([
            'school_id' => $schoolA->id,
            'plan' => 'starter',
            'status' => 'active',
            'provider' => 'stripe',
            'provider_reference' => 'CONTENT-SUPER-ACTIVE',
            'starts_at' => now(),
        ]);

        SchoolSubscription::create([
            'school_id' => $schoolB->id,
            'plan' => 'starter',
            'status' => 'trialing',
            'provider' => 'paystack',
            'provider_reference' => 'CONTENT-SUPER-TRIAL',
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays(10),
        ]);

        $pageForSchoolA = $this->pageForSchoolInSuperAdminList($schoolA->id);
        $pageForSchoolB = $this->pageForSchoolInSuperAdminList($schoolB->id);

        $responseA = $this->actingAs($superAdmin)->get('/dashboard/super-admin/schools?page=' . $pageForSchoolA);
        $responseA->assertStatus(200);
        $responseA->assertSee('Super Admin Schools');
        $responseA->assertSee('Schools');
        $responseA->assertSee($schoolA->slug);
        $responseA->assertSee('Active Subscriptions');

        $responseB = $this->actingAs($superAdmin)->get('/dashboard/super-admin/schools?page=' . $pageForSchoolB);
        $responseB->assertStatus(200);
        $responseB->assertSee($schoolB->slug);
    }

    public function test_super_admin_revenue_page_renders_subscription_breakdowns()
    {
        $superAdmin = $this->createUser('super_admin', null);
        $schoolA = $this->createSchool('content-revenue-a');
        $schoolB = $this->createSchool('content-revenue-b');

        SchoolSubscription::create([
            'school_id' => $schoolA->id,
            'plan' => 'starter',
            'status' => 'active',
            'provider' => 'stripe',
            'provider_reference' => 'CONTENT-REV-ACTIVE',
            'starts_at' => now(),
        ]);

        SchoolSubscription::create([
            'school_id' => $schoolB->id,
            'plan' => 'starter',
            'status' => 'trialing',
            'provider' => 'paystack',
            'provider_reference' => 'CONTENT-REV-TRIAL',
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($superAdmin)->get('/dashboard/super-admin/revenue');

        $response->assertStatus(200);
        $response->assertSee('Super Admin Revenue');
        $response->assertSee('Total Subscriptions');
        $response->assertSee('Subscriptions by Provider');
        $response->assertSee('Schools by Plan');
        $response->assertSee('stripe');
        $response->assertSee('paystack');
    }

    public function test_teacher_student_parent_and_affiliate_dashboards_render_core_sections()
    {
        $school = $this->createSchool('content-role-dashboards');
        $this->createSession($school->id, '2031/2032');

        $teacher = $this->createUser('teacher', $school->id);
        $student = $this->createUser('student', $school->id);
        $parent = $this->createUser('parent', $school->id);
        $affiliate = $this->createUser('affiliate', $school->id);

        StudentParentInfo::create([
            'student_id' => $student->id,
            'school_id' => $school->id,
            'father_name' => 'Parent Father',
            'father_phone' => $parent->phone,
            'mother_name' => 'Parent Mother',
            'mother_phone' => $parent->phone,
            'parent_address' => '123 Test Street',
        ]);

        $teacherResponse = $this->actingAs($teacher)->get('/dashboard/teacher');
        $teacherResponse->assertStatus(200);
        $teacherResponse->assertSee('Teacher Dashboard');
        $teacherResponse->assertSee('Recent Assignments');

        $studentResponse = $this->actingAs($student)->get('/dashboard/student');
        $studentResponse->assertStatus(200);
        $studentResponse->assertSee('Student Dashboard');
        $studentResponse->assertSee('Published Results');

        $parentResponse = $this->actingAs($parent)->get('/dashboard/parent');
        $parentResponse->assertStatus(200);
        $parentResponse->assertSee('Parent Dashboard');
        $parentResponse->assertSee('Linked Children');

        $affiliateResponse = $this->actingAs($affiliate)->get('/dashboard/affiliate');
        $affiliateResponse->assertStatus(200);
        $affiliateResponse->assertSee('Affiliate Dashboard');
        $affiliateResponse->assertSee('Next Integration Steps');
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

    private function createSession(int $schoolId, string $name): SchoolSession
    {
        return SchoolSession::create([
            'session_name' => $name,
            'school_id' => $schoolId,
        ]);
    }

    private function createUser(string $role, ?int $schoolId): User
    {
        return User::factory()->create([
            'role' => $role,
            'school_id' => $schoolId,
        ]);
    }

    private function pageForSchoolInSuperAdminList(int $schoolId, int $perPage = 15): int
    {
        $orderedSchoolIds = School::orderBy('name')->pluck('id')->values();
        $schoolIndex = $orderedSchoolIds->search($schoolId);

        $this->assertNotFalse($schoolIndex);

        return intdiv((int) $schoolIndex, $perPage) + 1;
    }
}
