<?php

namespace App\Providers;

use App\Models\Promotion;
use App\Models\SchoolClass;
use App\Models\SchoolSession;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            if (!Auth::check()) {
                return;
            }

            $latestSession = null;

            try {
                $latestSession = SchoolSession::query()->latest('id')->first();
            } catch (Throwable $e) {
                // Fail open for shared layout rendering when DB is temporarily unavailable.
            }

            $currentSchoolSessionName = optional($latestSession)->session_name;
            $browseSessionName = session('browse_session_name');
            $isBrowsingAnotherSession = session()->has('browse_session_name') && $browseSessionName !== $currentSchoolSessionName;

            $view->with([
                'currentSchoolSessionName' => $currentSchoolSessionName,
                'browseSessionName' => $browseSessionName,
                'isBrowsingAnotherSession' => $isBrowsingAnotherSession,
                'hasAnySchoolSession' => (bool) $latestSession,
            ]);
        });

        View::composer('layouts.left-menu', function ($view) {
            if (!Auth::check()) {
                return;
            }

            $browseSessionId = session('browse_session_id');
            $classCount = 0;
            $studentRoutineClassInfo = null;

            try {
                $effectiveSessionId = $browseSessionId ?: SchoolSession::query()->latest('id')->value('id');

                if (Auth::user()->can('view classes') && $effectiveSessionId) {
                    $classCount = SchoolClass::query()->where('session_id', $effectiveSessionId)->count();
                }

                if (Auth::user()->role === 'student' && $effectiveSessionId) {
                    $studentRoutineClassInfo = Promotion::query()
                        ->where('session_id', $effectiveSessionId)
                        ->where('student_id', Auth::id())
                        ->first();
                }
            } catch (Throwable $e) {
                // Keep lightweight defaults if a transient DB issue happens.
            }

            $view->with([
                'menuClassCount' => $classCount,
                'studentRoutineClassInfo' => $studentRoutineClassInfo,
            ]);
        });
    }
}
