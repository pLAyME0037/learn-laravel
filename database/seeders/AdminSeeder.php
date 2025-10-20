<?php
namespace Database\Seeders;

use App\Models\Role;
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
        $roleId = Role::where('name', 'Super Administrator')->first();
        // Get the super admin role
        $superAdminRole = SpatieRole::where('name', 'Super Administrator')->first();

        if (! $superAdminRole) {
            $this->command->error('Super admin role not found! Please run PermissionSeeder first.');
            return;
        }

        // Find the user by username and update or create it
        $user = User::updateOrCreate(
            ['username' => 'superuser'],
            [
                'name'              => 'Super Administrator',
                'email'             => 'superadmin@example.com',
                'password'          => Hash::make('password'),
                'bio'               => 'Super Administrator with full access rights.',
                'email_verified_at' => now(),
                'is_active'         => true,
                'role_id'           => $roleId->getKey(),
            ]
        );

        // Assign the Spatie role 'Super Administrator' directly
        // Ensure the Spatie role 'Super Administrator' exists before assigning
        if ($superAdminRole && ! $user->hasRole($superAdminRole->name)) {
            // Directly attach the role to the user's roles relationship to bypass custom assignRole method
            $user->roles()->attach($superAdminRole->id);
        }

        $this->command->info('Admin user created/updated with Super Administrator role.');
    }
}
