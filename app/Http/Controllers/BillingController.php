<?php

namespace App\Http\Controllers;

use App\Models\SchoolSubscription;
use App\Models\SchoolSubscriptionWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function setup()
    {
        $schoolId = Auth::user()->school_id;

        $subscription = SchoolSubscription::where('school_id', $schoolId)
            ->latest('id')
            ->first();

        if (!$subscription) {
            $subscription = SchoolSubscription::create([
                'school_id' => $schoolId,
                'plan' => 'trial',
                'status' => 'trialing',
                'trial_ends_at' => now()->addDays((int) env('SCHOOL_TRIAL_DAYS', 14)),
                'starts_at' => now(),
            ]);
        }

        return view('billing.setup', [
            'subscription' => $subscription,
        ]);
    }

    public function startCheckout(Request $request)
    {
        $validated = $request->validate([
            'plan' => ['required', 'in:starter,growth,enterprise'],
            'provider' => ['required', 'in:stripe,paystack'],
        ]);

        $schoolId = Auth::user()->school_id;

        $subscription = SchoolSubscription::where('school_id', $schoolId)
            ->latest('id')
            ->firstOrFail();

        $subscription->update([
            'plan' => $validated['plan'],
            'provider' => $validated['provider'],
            'status' => 'pending_checkout',
            'provider_reference' => strtoupper($validated['provider']) . '-PENDING-' . now()->timestamp,
        ]);

        $redirectBase = $validated['provider'] === 'stripe'
            ? env('STRIPE_CHECKOUT_URL', '#')
            : env('PAYSTACK_CHECKOUT_URL', '#');

        return redirect()
            ->route('billing.setup.show')
            ->with('success', 'Checkout initiated. Provider: ' . strtoupper($validated['provider']) . '. Reference: ' . $subscription->provider_reference)
            ->with('checkout_url', $redirectBase);
    }

    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $eventType = $request->input('type', 'unknown');

        $reference = $request->input('data.object.metadata.provider_reference')
            ?? $request->input('data.object.client_reference_id')
            ?? $request->input('provider_reference');

        $signatureValid = $this->isStripeSignatureValid($request, $payload);

        if (!$signatureValid) {
            $this->logWebhookEvent('stripe', $eventType, $reference, $payload, false, null, null);

            return response()->json(['message' => 'Unauthorized webhook'], 401);
        }

        if (!$reference) {
            $this->logWebhookEvent('stripe', $eventType, null, $payload, true, null, null);

            return response()->json(['message' => 'No provider reference supplied'], 400);
        }

        $subscription = SchoolSubscription::where('provider_reference', $reference)
            ->latest('id')
            ->first();

        if (!$subscription) {
            $this->logWebhookEvent('stripe', $eventType, $reference, $payload, true, null, null);

            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $statusAfterUpdate = $subscription->status;

        if (in_array($eventType, ['checkout.session.completed', 'invoice.paid'], true)) {
            $subscription->update([
                'status' => 'active',
                'starts_at' => $subscription->starts_at ?? now(),
            ]);

            $statusAfterUpdate = 'active';
        } elseif (in_array($eventType, ['invoice.payment_failed'], true)) {
            $subscription->update(['status' => 'past_due']);

            $statusAfterUpdate = 'past_due';
        } elseif (in_array($eventType, ['customer.subscription.deleted'], true)) {
            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);

            $statusAfterUpdate = 'canceled';
        }

        $this->logWebhookEvent('stripe', $eventType, $reference, $payload, true, $subscription->id, $statusAfterUpdate);

        return response()->json(['message' => 'Webhook processed']);
    }

    public function paystackWebhook(Request $request)
    {
        $payload = $request->getContent();
        $eventType = $request->input('event', 'unknown');

        $reference = $request->input('data.metadata.provider_reference')
            ?? $request->input('data.reference')
            ?? $request->input('provider_reference');

        $signatureValid = $this->isPaystackSignatureValid($request, $payload);

        if (!$signatureValid) {
            $this->logWebhookEvent('paystack', $eventType, $reference, $payload, false, null, null);

            return response()->json(['message' => 'Unauthorized webhook'], 401);
        }

        if (!$reference) {
            $this->logWebhookEvent('paystack', $eventType, null, $payload, true, null, null);

            return response()->json(['message' => 'No provider reference supplied'], 400);
        }

        $subscription = SchoolSubscription::where('provider_reference', $reference)
            ->latest('id')
            ->first();

        if (!$subscription) {
            $this->logWebhookEvent('paystack', $eventType, $reference, $payload, true, null, null);

            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $statusAfterUpdate = $subscription->status;

        if (in_array($eventType, ['charge.success', 'subscription.create'], true)) {
            $subscription->update([
                'status' => 'active',
                'starts_at' => $subscription->starts_at ?? now(),
            ]);

            $statusAfterUpdate = 'active';
        } elseif (in_array($eventType, ['invoice.payment_failed'], true)) {
            $subscription->update(['status' => 'past_due']);

            $statusAfterUpdate = 'past_due';
        } elseif (in_array($eventType, ['subscription.not_renew', 'subscription.disable'], true)) {
            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);

            $statusAfterUpdate = 'canceled';
        }

        $this->logWebhookEvent('paystack', $eventType, $reference, $payload, true, $subscription->id, $statusAfterUpdate);

        return response()->json(['message' => 'Webhook processed']);
    }

    private function isStripeSignatureValid(Request $request, string $payload): bool
    {
        $secret = env('STRIPE_WEBHOOK_SECRET');

        if (!$secret) {
            return true;
        }

        $signatureHeader = (string) $request->header('Stripe-Signature', '');

        if ($signatureHeader === '') {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signatureHeader) as $part) {
            $part = trim($part);

            if (strpos($part, 't=') === 0) {
                $timestamp = substr($part, 2);
            }

            if (strpos($part, 'v1=') === 0) {
                $signatures[] = substr($part, 3);
            }
        }

        if (!$timestamp || empty($signatures)) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expectedSignature, $signature)) {
                return true;
            }
        }

        return false;
    }

    private function isPaystackSignatureValid(Request $request, string $payload): bool
    {
        $secret = env('PAYSTACK_SECRET_KEY', env('PAYSTACK_WEBHOOK_SECRET'));

        if (!$secret) {
            return true;
        }

        $incoming = (string) $request->header('X-Paystack-Signature', '');

        if ($incoming === '') {
            return false;
        }

        $expected = hash_hmac('sha512', $payload, $secret);

        return hash_equals($expected, $incoming);
    }

    private function logWebhookEvent(
        string $provider,
        string $eventType,
        ?string $providerReference,
        string $payload,
        bool $signatureValid,
        ?int $subscriptionId,
        ?string $statusAfter
    ): void {
        SchoolSubscriptionWebhookEvent::create([
            'school_subscription_id' => $subscriptionId,
            'provider' => $provider,
            'event_type' => $eventType,
            'provider_reference' => $providerReference,
            'payload' => $payload,
            'signature_valid' => $signatureValid,
            'status_after' => $statusAfter,
            'processed_at' => now(),
        ]);
    }

}
