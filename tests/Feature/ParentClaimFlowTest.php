<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\StudentParentInfo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ParentClaimFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_claim_page_loads_for_valid_code()
    {
        $school = School::create([
            'name' => 'Claim School',
            'slug' => 'claim-school-' . Str::lower(Str::random(6)),
            'status' => 'active',
            'plan' => 'trial',
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'school_id' => $school->id,
        ]);

        StudentParentInfo::create([
            'student_id' => $student->id,
            'father_name' => 'John Doe',
            'father_phone' => '1234567890',
            'mother_name' => 'Jane Doe',
            'mother_phone' => '0987654321',
            'parent_address' => '12 Parent Street',
            'claim_code' => 'CHD-TEST-CODE-1',
            'claim_code_generated_at' => now(),
            'school_id' => $school->id,
        ]);

        $response = $this->get(route('parent.claim.show', ['code' => 'CHD-TEST-CODE-1']));

        $response->assertStatus(200);
        $response->assertSee('Parent Account Setup');
    }

    public function test_parent_can_claim_child_and_create_parent_account()
    {
        $school = School::create([
            'name' => 'Claim School',
            'slug' => 'claim-school-' . Str::lower(Str::random(6)),
            'status' => 'active',
            'plan' => 'trial',
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'school_id' => $school->id,
        ]);

        $parentInfo = StudentParentInfo::create([
            'student_id' => $student->id,
            'father_name' => 'John Doe',
            'father_phone' => '1234567890',
            'mother_name' => 'Jane Doe',
            'mother_phone' => '0987654321',
            'parent_address' => '12 Parent Street',
            'claim_code' => 'CHD-TEST-CODE-2',
            'claim_code_generated_at' => now(),
            'school_id' => $school->id,
        ]);

        $response = $this->post(route('parent.claim.store', ['code' => 'CHD-TEST-CODE-2']), [
            'first_name' => 'Parent',
            'last_name' => 'User',
            'email' => 'parent.claim@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '5551112222',
            'address' => 'Parent Address',
            'address2' => '',
            'city' => 'Lagos',
            'zip' => '100001',
            'gender' => 'Male',
            'nationality' => 'Nigerian',
        ]);

        $response->assertRedirect(route('dashboard.parent'));

        $this->assertDatabaseHas('users', [
            'email' => 'parent.claim@example.com',
            'role' => 'parent',
            'school_id' => $school->id,
        ]);

        $parentInfo->refresh();

        $this->assertNotNull($parentInfo->parent_user_id);
        $this->assertNotNull($parentInfo->claim_code_claimed_at);
        $this->assertEquals('parent.claim@example.com', $parentInfo->parent_email);
    }
}
