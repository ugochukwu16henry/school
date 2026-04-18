<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureSchoolAccess
{
    /**
     * Ensure tenant users have a school context. Super admin bypasses tenant constraint.
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
            abort(403, 'School context is missing for this account.');
        }

        return $next($request);
    }
}
