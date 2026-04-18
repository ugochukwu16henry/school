<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $parts = preg_split('/\s+/', trim($data['name']), 2, PREG_SPLIT_NO_EMPTY);
        $first = $parts[0] ?? 'User';
        $last = $parts[1] ?? 'User';

        $schoolId = School::where('slug', 'default-school')->value('id');
        if (! $schoolId) {
            $school = School::create([
                'name' => 'Default School',
                'slug' => 'default-school',
                'status' => 'active',
                'plan' => 'trial',
            ]);
            $schoolId = $school->id;
        }

        return User::create([
            'first_name' => $first,
            'last_name' => $last,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'gender' => 'Male',
            'nationality' => 'Nigerian',
            'phone' => '—',
            'address' => '—',
            'address2' => '—',
            'city' => '—',
            'zip' => '000000',
            'role' => 'student',
            'school_id' => $schoolId,
        ]);
    }
}
