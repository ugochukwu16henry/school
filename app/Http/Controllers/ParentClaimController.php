<?php

namespace App\Http\Controllers;

use App\Models\StudentParentInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ParentClaimController extends Controller
{
    public function show(string $code)
    {
        $claim = StudentParentInfo::with(['student'])
            ->where('claim_code', $code)
            ->first();

        if (!$claim || $claim->claim_code_claimed_at) {
            return redirect()->route('login')->with('error', 'Invalid or already claimed child code.');
        }

        return view('auth.parent-claim', [
            'claim' => $claim,
            'code' => $code,
        ]);
    }

    public function store(Request $request, string $code)
    {
        $claim = StudentParentInfo::with(['student'])
            ->where('claim_code', $code)
            ->first();

        if (!$claim || $claim->claim_code_claimed_at) {
            return redirect()->route('login')->with('error', 'Invalid or already claimed child code.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'gender' => 'required|string|max:50',
            'nationality' => 'required|string|max:255',
        ]);

        $parent = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'],
            'nationality' => $validated['nationality'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'address2' => $validated['address2'] ?? '',
            'city' => $validated['city'],
            'zip' => $validated['zip'],
            'role' => 'parent',
            'school_id' => $claim->school_id,
        ]);

        $claim->update([
            'parent_user_id' => $parent->id,
            'parent_email' => $parent->email,
            'claim_code_claimed_at' => now(),
        ]);

        Auth::login($parent);

        return redirect()->route('dashboard.parent')->with('status', 'Parent account created and child linked successfully.');
    }
}
