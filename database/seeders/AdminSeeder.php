<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the super admin role
        $superAdminRole = DB::table('roles')->where('slug', 'super_admin')->first();

        if (!User::where('email', 'admin@university.edu')->exists()) {
            User::create([
                'role_id' => $superAdminRole->id,
                'name' => 'System Administrator',
                'username' => 'superadmin',
                'email' => 'admin@university.edu',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }
    }
}
