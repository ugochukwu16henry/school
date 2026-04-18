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

    public function test_admin_is_redirected_to_billing_when_subscription_is_past_due()
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

        $response->assertRedirect(route('billing.setup.show'));
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

    public function test_expired_trial_is_converted_to_past_due_and_redirected_to_billing()
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

        $response->assertRedirect(route('billing.setup.show'));

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

    private function createSchool(string $slug): School
    {
        return School::create([
            'name' => 'School ' . $slug,
            'slug' => $slug,
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
