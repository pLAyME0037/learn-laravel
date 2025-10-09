<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::where('email', 'staff@example.com')->exists()) {
            User::create([
                'profile_pic'       => "https://ui-avatars.com/api/?name=".urlencode('Staff')."&color=7F9CF5&background=EBF4FF",
                'username'          => 'staff',
                'name'              => 'Staff User',
                'email'             => 'staff@example.com',
                'role'              => 'staff',
                'bio'               => 'This is a staff user account',
                'email_verified_at' => now(),
                'password'          => Hash::make('password')
            ]);
        }
    }
}
