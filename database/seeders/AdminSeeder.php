<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as SpatieRole;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the super admin role
        $superAdminRole = SpatieRole::where('name', 'super_admin')->first();

        if (! $superAdminRole) {
            $this->command->error('Super admin role not found! Please run PermissionSeeder first.');
            return;
        }

        // Find the user by username and update or create it
        $user = User::updateOrCreate(
            ['username' => 'superuser'],
            [
                'name'              => 'System Administrator',
                'email'             => 'superadmin@example.com',
                'password'          => Hash::make('password'),
                'bio'               => 'System Administrator with full access rights.',
                'email_verified_at' => now(),
                'is_active'         => true,
            ]
        );

        // Assign the role to the user
        $user->assignRole($superAdminRole);

        $this->command->info('Admin user created/updated with super_admin role.');
    }
}
