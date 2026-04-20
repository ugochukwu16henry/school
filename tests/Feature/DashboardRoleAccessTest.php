<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SchoolSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_school_admin_sections()
    {
        $school = $this->createSchool('admin-sections');
        $this->createSession($school->id, '2026/2027');
        $admin = $this->createUser('admin', $school->id);

        $this->actingAs($admin)->get('/school/overview')->assertStatus(200);
        $this->actingAs($admin)->get('/school/people')->assertStatus(200);
        $this->actingAs($admin)->get('/school/operations')->assertStatus(200);
    }

    public function test_non_admin_cannot_access_school_admin_sections()
    {
        $school = $this->createSchool('admin-sections-forbidden');
        $teacher = $this->createUser('teacher', $school->id);

        $this->actingAs($teacher)->get('/school/overview')->assertStatus(403);
        $this->actingAs($teacher)->get('/school/people')->assertStatus(403);
        $this->actingAs($teacher)->get('/school/operations')->assertStatus(403);
    }

    public function test_super_admin_can_access_super_admin_extended_pages()
    {
        $superAdmin = $this->createUser('super_admin', null);

        $this->actingAs($superAdmin)->get('/dashboard/super-admin')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/dashboard/super-admin/schools')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/dashboard/super-admin/revenue')->assertStatus(200);
    }

    public function test_non_super_admin_cannot_access_super_admin_extended_pages()
    {
        $school = $this->createSchool('super-admin-forbidden');
        $admin = $this->createUser('admin', $school->id);

        $this->actingAs($admin)->get('/dashboard/super-admin/schools')->assertStatus(403);
        $this->actingAs($admin)->get('/dashboard/super-admin/revenue')->assertStatus(403);
    }

    public function test_affiliate_can_access_affiliate_dashboard()
    {
        $school = $this->createSchool('affiliate-dashboard');
        $affiliate = $this->createUser('affiliate', $school->id);

        $this->actingAs($affiliate)->get('/dashboard/affiliate')->assertStatus(200);
    }

    public function test_non_affiliate_cannot_access_affiliate_dashboard()
    {
        $school = $this->createSchool('affiliate-dashboard-forbidden');
        $admin = $this->createUser('admin', $school->id);

        $this->actingAs($admin)->get('/dashboard/affiliate')->assertStatus(403);
    }

    public function test_home_redirects_by_role_for_affiliate()
    {
        $school = $this->createSchool('affiliate-home-redirect');
        $affiliate = $this->createUser('affiliate', $school->id);

        $this->actingAs($affiliate)
            ->get('/home')
            ->assertRedirect(route('dashboard.affiliate'));
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
}
