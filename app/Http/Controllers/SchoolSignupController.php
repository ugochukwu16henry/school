<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolSignupController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function create()
    {
        return view('schools.signup');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'gender' => ['required', 'in:Male,Female'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $trialDays = (int) env('SCHOOL_TRIAL_DAYS', 14);

        $result = DB::transaction(function () use ($validated, $trialDays) {
            $baseSlug = Str::slug($validated['school_name']);
            if ($baseSlug === '') {
                $baseSlug = 'school';
            }

            $slug = $baseSlug;
            $index = 2;

            while (School::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $index;
                $index++;
            }

            $school = School::create([
                'name' => $validated['school_name'],
                'slug' => $slug,
                'status' => 'active',
                'plan' => 'trial',
                'trial_ends_at' => now()->addDays($trialDays),
            ]);

            $admin = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'gender' => $validated['gender'],
                'nationality' => 'Nigerian',
                'phone' => $validated['phone'],
                'address' => $validated['school_name'] . ' Campus',
                'address2' => 'Main Campus',
                'city' => 'Lagos',
                'zip' => '100001',
                'role' => 'admin',
                'school_id' => $school->id,
            ]);

            SchoolSubscription::create([
                'school_id' => $school->id,
                'plan' => 'trial',
                'status' => 'trialing',
                'trial_ends_at' => $school->trial_ends_at,
                'starts_at' => now(),
            ]);

            return [$school, $admin];
        });

        [$school, $admin] = $result;

        Auth::login($admin);

        return redirect()
            ->route('school.setup.show')
            ->with('success', 'School created successfully. Welcome to ' . $school->name . '.');
    }
}
