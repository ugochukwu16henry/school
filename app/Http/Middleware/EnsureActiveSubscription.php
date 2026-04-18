<?php

namespace App\Http\Middleware;

use App\Models\SchoolSubscription;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class EnsureActiveSubscription
{
    /**
     * Ensure school users have an active or valid trial subscription.
     * Super admin bypasses this middleware.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->role === 'super_admin') {
            return $next($request);
        }

        if (!$user->school_id) {
            return redirect()->route('billing.setup.show')
                ->with('error', 'School billing profile is missing.');
        }

        $freeStudentLimit = (int) config('billing.free_student_limit', 50);
        $studentCount = User::where('school_id', $user->school_id)
            ->where('role', 'student')
            ->count();

        $subscription = SchoolSubscription::where('school_id', $user->school_id)
            ->latest('id')
            ->first();

        if ($subscription && $subscription->status === 'trialing') {
            if ($subscription->trial_ends_at && now()->gt($subscription->trial_ends_at)) {
                $subscription->update(['status' => 'past_due']);
                $subscription->refresh();
            }
        }

        if ($freeStudentLimit > 0) {
            if ($studentCount <= $freeStudentLimit) {
                return $next($request);
            }
        }

        if (!$subscription) {
            return redirect()->route('billing.setup.show')
                ->with('error', 'Please configure billing to continue.');
        }

        if ($subscription->status === 'active') {
            return $next($request);
        }

        if ($subscription->status === 'trialing') {
            if (!$subscription->trial_ends_at || now()->lte($subscription->trial_ends_at)) {
                return $next($request);
            }

            $subscription->update(['status' => 'past_due']);
        }

        return redirect()->route('billing.setup.show')
            ->with('error', 'Your subscription is not active. Please complete billing.');
    }
}
