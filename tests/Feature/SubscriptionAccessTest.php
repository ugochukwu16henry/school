<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_with_past_due_subscription_can_enter_when_within_free_student_limit()
    {
        $school = $this->createSchool('past-due-school');

        SchoolSubscription::create([
            'school_id' => $school->id,
            'plan' => 'starter',
            'status' => 'past_due',
            'provider' => 'stripe',
            'provider_reference' => 'STRIPE-PASTDUE-1',
            'starts_at' => now(),
        ]);

        $admin = $this->createUser('admin', $school->id);

        $response = $this->actingAs($admin)->get('/home');

        $response->assertRedirect(route('school.setup.show'));
    }

    public function test_admin_with_active_subscription_can_enter_protected_area()
    {
        $school = $this->createSchool('active-school');

        SchoolSubscription::create([
            'school_id' => $school->id,
            'plan' => 'starter',
            'status' => 'active',
            'provider' => 'stripe',
            'provider_reference' => 'STRIPE-ACTIVE-1',
            'starts_at' => now(),
        ]);

        $admin = $this->createUser('admin', $school->id);

        $response = $this->actingAs($admin)->get('/home');

        $response->assertRedirect(route('school.setup.show'));
    }

    public function test_expired_trial_is_converted_to_past_due_and_still_allows_access_within_free_student_limit()
    {
        $school = $this->createSchool('expired-trial-school');

        $subscription = SchoolSubscription::create([
            'school_id' => $school->id,
            'plan' => 'starter',
            'status' => 'trialing',
            'provider' => 'paystack',
            'provider_reference' => 'PAYSTACK-TRIAL-EXPIRED-1',
            'trial_ends_at' => now()->subDay(),
            'starts_at' => now()->subDays(10),
        ]);

        $admin = $this->createUser('admin', $school->id);

        $response = $this->actingAs($admin)->get('/home');

        $response->assertRedirect(route('school.setup.show'));

        $this->assertDatabaseHas('school_subscriptions', [
            'id' => $subscription->id,
            'status' => 'past_due',
        ]);
    }

    public function test_super_admin_bypasses_subscription_gate()
    {
        $superAdmin = $this->createUser('super_admin', null);

        $response = $this->actingAs($superAdmin)->get('/home');

        $response->assertRedirect(route('dashboard.super-admin'));
    }

    public function test_admin_with_non_active_subscription_can_enter_when_school_has_50_or_fewer_students()
    {
        $school = $this->createSchool('free-tier-50');

        SchoolSubscription::create([
            'school_id' => $school->id,
            'plan' => 'fair_growth',
            'status' => 'past_due',
            'provider' => 'stripe',
            'provider_reference' => 'STRIPE-FREE-TIER-50',
            'starts_at' => now(),
        ]);

        $admin = $this->createUser('admin', $school->id);
        User::factory()->count(50)->create([
            'role' => 'student',
            'school_id' => $school->id,
        ]);

        $response = $this->actingAs($admin)->get('/home');

        $response->assertRedirect(route('school.setup.show'));
    }

    public function test_admin_with_non_active_subscription_is_redirected_when_school_has_more_than_50_students()
    {
        $school = $this->createSchool('free-tier-51');

        SchoolSubscription::create([
            'school_id' => $school->id,
            'plan' => 'fair_growth',
            'status' => 'past_due',
            'provider' => 'stripe',
            'provider_reference' => 'STRIPE-FREE-TIER-51',
            'starts_at' => now(),
        ]);

        $admin = $this->createUser('admin', $school->id);
        User::factory()->count(51)->create([
            'role' => 'student',
            'school_id' => $school->id,
        ]);

        $response = $this->actingAs($admin)->get('/home');

        $response->assertRedirect(route('billing.setup.show'));
    }

    private function createSchool(string $slug): School
    {
        $uniqueSlug = $slug . '-' . uniqid();

        return School::create([
            'name' => 'School ' . $uniqueSlug,
            'slug' => $uniqueSlug,
            'status' => 'active',
            'plan' => 'trial',
            'trial_ends_at' => now()->addDays(14),
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
