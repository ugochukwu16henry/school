<?php

namespace App\Http\Controllers;

use App\Models\SchoolSubscription;
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
        $reference = $request->input('data.object.metadata.provider_reference')
            ?? $request->input('data.object.client_reference_id')
            ?? $request->input('provider_reference');

        if (!$reference) {
            return response()->json(['message' => 'No provider reference supplied'], 400);
        }

        $subscription = SchoolSubscription::where('provider_reference', $reference)
            ->latest('id')
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $eventType = $request->input('type', 'unknown');

        if (in_array($eventType, ['checkout.session.completed', 'invoice.paid'], true)) {
            $subscription->update([
                'status' => 'active',
                'starts_at' => $subscription->starts_at ?? now(),
            ]);
        } elseif (in_array($eventType, ['invoice.payment_failed'], true)) {
            $subscription->update(['status' => 'past_due']);
        } elseif (in_array($eventType, ['customer.subscription.deleted'], true)) {
            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    public function paystackWebhook(Request $request)
    {
        $reference = $request->input('data.metadata.provider_reference')
            ?? $request->input('data.reference')
            ?? $request->input('provider_reference');

        if (!$reference) {
            return response()->json(['message' => 'No provider reference supplied'], 400);
        }

        $subscription = SchoolSubscription::where('provider_reference', $reference)
            ->latest('id')
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found'], 404);
        }

        $eventType = $request->input('event', 'unknown');

        if (in_array($eventType, ['charge.success', 'subscription.create'], true)) {
            $subscription->update([
                'status' => 'active',
                'starts_at' => $subscription->starts_at ?? now(),
            ]);
        } elseif (in_array($eventType, ['invoice.payment_failed'], true)) {
            $subscription->update(['status' => 'past_due']);
        } elseif (in_array($eventType, ['subscription.not_renew', 'subscription.disable'], true)) {
            $subscription->update([
                'status' => 'canceled',
                'ends_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
