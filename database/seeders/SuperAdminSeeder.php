<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = env('SUPER_ADMIN_EMAIL', 'owner@schoolapp.local');
        $password = env('SUPER_ADMIN_PASSWORD', 'ChangeMe123!');

        User::updateOrCreate(
            ['email' => $email],
            [
                'first_name' => 'Platform',
                'last_name' => 'Owner',
                'password' => Hash::make($password),
                'gender' => 'Male',
                'nationality' => 'Nigerian',
                'phone' => '0000000000',
                'address' => 'Platform HQ',
                'address2' => 'Global',
                'city' => 'Lagos',
                'zip' => '100001',
                'role' => 'super_admin',
                'school_id' => null,
            ]
        );
    }
}
