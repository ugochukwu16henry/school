<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SchoolSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_stripe_webhook_rejects_invalid_signature_when_secret_is_set()
    {
        $subscription = $this->createSubscription('stripe', 'STRIPE-PENDING-1001');

        $this->setWebhookEnv('STRIPE_WEBHOOK_SECRET', 'whsec_test');

        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'client_reference_id' => $subscription->provider_reference,
                ],
            ],
        ]);

        $response = $this->call('POST', '/billing/webhook/stripe', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => 't=1713456000,v1=invalidsignature',
        ], $payload);

        $response->assertStatus(401);

        $this->assertDatabaseHas('school_subscriptions', [
            'id' => $subscription->id,
            'status' => 'pending_checkout',
        ]);

        $this->assertDatabaseHas('school_subscription_webhook_events', [
            'provider' => 'stripe',
            'event_type' => 'checkout.session.completed',
            'provider_reference' => $subscription->provider_reference,
            'signature_valid' => 0,
        ]);
    }

    public function test_stripe_webhook_activates_subscription_with_valid_signature()
    {
        $subscription = $this->createSubscription('stripe', 'STRIPE-PENDING-1002');

        $this->setWebhookEnv('STRIPE_WEBHOOK_SECRET', 'whsec_test');

        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'metadata' => [
                        'provider_reference' => $subscription->provider_reference,
                    ],
                ],
            ],
        ]);

        $timestamp = 1713456001;
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, 'whsec_test');

        $response = $this->call('POST', '/billing/webhook/stripe', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => 't=' . $timestamp . ',v1=' . $signature,
        ], $payload);

        $response->assertOk();

        $this->assertDatabaseHas('school_subscriptions', [
            'id' => $subscription->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('school_subscription_webhook_events', [
            'provider' => 'stripe',
            'event_type' => 'checkout.session.completed',
            'provider_reference' => $subscription->provider_reference,
            'signature_valid' => 1,
            'status_after' => 'active',
        ]);
    }

    public function test_paystack_webhook_cancels_subscription_with_valid_signature()
    {
        $subscription = $this->createSubscription('paystack', 'PAYSTACK-PENDING-9001');

        $this->setWebhookEnv('PAYSTACK_SECRET_KEY', 'sk_test_paystack');

        $payload = json_encode([
            'event' => 'subscription.disable',
            'data' => [
                'metadata' => [
                    'provider_reference' => $subscription->provider_reference,
                ],
            ],
        ]);

        $signature = hash_hmac('sha512', $payload, 'sk_test_paystack');

        $response = $this->call('POST', '/billing/webhook/paystack', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
        ], $payload);

        $response->assertOk();

        $this->assertDatabaseHas('school_subscriptions', [
            'id' => $subscription->id,
            'status' => 'canceled',
        ]);

        $this->assertDatabaseHas('school_subscription_webhook_events', [
            'provider' => 'paystack',
            'event_type' => 'subscription.disable',
            'provider_reference' => $subscription->provider_reference,
            'signature_valid' => 1,
            'status_after' => 'canceled',
        ]);
    }

    public function test_stripe_webhook_marks_subscription_past_due_on_payment_failed_event()
    {
        $subscription = $this->createSubscription('stripe', 'STRIPE-PENDING-1003');

        $this->setWebhookEnv('STRIPE_WEBHOOK_SECRET', 'whsec_test');

        $payload = json_encode([
            'type' => 'invoice.payment_failed',
            'data' => [
                'object' => [
                    'metadata' => [
                        'provider_reference' => $subscription->provider_reference,
                    ],
                ],
            ],
        ]);

        $timestamp = 1713456002;
        $signature = hash_hmac('sha256', $timestamp . '.' . $payload, 'whsec_test');

        $response = $this->call('POST', '/billing/webhook/stripe', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => 't=' . $timestamp . ',v1=' . $signature,
        ], $payload);

        $response->assertOk();

        $this->assertDatabaseHas('school_subscriptions', [
            'id' => $subscription->id,
            'status' => 'past_due',
        ]);

        $this->assertDatabaseHas('school_subscription_webhook_events', [
            'provider' => 'stripe',
            'event_type' => 'invoice.payment_failed',
            'provider_reference' => $subscription->provider_reference,
            'signature_valid' => 1,
            'status_after' => 'past_due',
        ]);
    }

    public function test_paystack_webhook_returns_400_when_reference_is_missing()
    {
        $this->setWebhookEnv('PAYSTACK_SECRET_KEY', 'sk_test_paystack');

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => [
                'metadata' => [
                    'note' => 'no reference in payload',
                ],
            ],
        ]);

        $signature = hash_hmac('sha512', $payload, 'sk_test_paystack');

        $response = $this->call('POST', '/billing/webhook/paystack', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
        ], $payload);

        $response->assertStatus(400);

        $this->assertDatabaseHas('school_subscription_webhook_events', [
            'provider' => 'paystack',
            'event_type' => 'charge.success',
            'provider_reference' => null,
            'signature_valid' => 1,
        ]);
    }

    private function createSubscription(string $provider, string $reference): SchoolSubscription
    {
        $school = School::create([
            'name' => 'Test School ' . $reference,
            'slug' => strtolower($reference),
            'status' => 'active',
            'plan' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        return SchoolSubscription::create([
            'school_id' => $school->id,
            'plan' => 'starter',
            'status' => 'pending_checkout',
            'provider' => $provider,
            'provider_reference' => $reference,
            'trial_ends_at' => now()->addDays(14),
            'starts_at' => now(),
        ]);
    }

    private function setWebhookEnv(string $key, string $value): void
    {
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
