<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder is optimized for performance and maintainability.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Get Config
        $permissionsConfig = $this->getPermissionsConfig();

        // 2. Seed Permissions
        $this->seedPermissions($permissionsConfig);

        // 3. Seed Roles & Assignments
        $rolesConfig = $this->getRolesConfig($permissionsConfig);
        $this->seedRolesAndAssignPermissions($rolesConfig);

        // 4. Create Super Admin
        $this->seedSuperAdminUser();
    }

    /**
     * Prepares and inserts all application permissions in a single database query.
     */
    private function seedPermissions(array $permissionsConfig): void
    {
        $permissionsToInsert = [];
        $now                 = now();

        foreach ($permissionsConfig as $group => $permissions) {
            foreach ($permissions as $permissionName => $description) {
                $permissionsToInsert[] = [
                    'name'        => $permissionName,
                    'group'       => $group,
                    'description' => $description,
                    'guard_name'  => 'web', // Default guard
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
        }

        // Use insert() for a single, efficient query.
        // Use insertOrIgnore() if the seeder might be run multiple times to avoid errors.
        Permission::insertOrIgnore($permissionsToInsert);
    }

    /**
     * Creates roles and syncs their permissions.
     */
    private function seedRolesAndAssignPermissions(array $rolesConfig): void
    {
        foreach ($rolesConfig as $roleName => $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                [
                    'description'    => $roleData['description'],
                    'is_system_role' => true,
                ]
            );

            // Using syncPermissions is idempotent and efficient for seeders.
            $role->syncPermissions($roleData['permissions']);
        }
    }

    /**
     * Creates the super administrator user if it does not already exist.
     */
    private function seedSuperAdminUser(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superuser@example.com'],
            [
                'name'     => 'Super Administrator',
                'username' => 'superuser',
                'password' => Hash::make('password'),
            ]
        );

        // Use the standard Spatie method to assign the role.
        $superAdmin->assignRole('Super Administrator');
    }

    /**
     * Centralized configuration for all permissions.
     * This structure eliminates the need for separate getPermissionGroup and getPermissionDescription methods.
     *
     * @return array
     */
    private function getPermissionsConfig(): array
    {
        return [
            // --- CORE ---
            'Admin & Staff'       => [
                'Administering' => 'View Admin Work',
            ],
            'User Management'       => [
                'Manage Users' => 'Create Delete Edit new user accounts',
            ],
            'Role Management'       => [
                'Manage Roles & Permissions'   => 'This User can (Create, Edit, Update, Delete) roles and permissions',
            ],

            // --- ACADEMIC STRUCTURE ---
            'Academic Structure'    => [
                'Manage Academic Structure'  => 'Manage structure (Faculties/Depts/Majors)',
            ],
            'Course Management'     => [
                'Manage Course'   => 'View course catalog',
            ],
            'Operation Management' => [
                'Manage Operation'   => 'Manage Class Schdule and Batch Enrollment',
            ],

            // --- PEOPLE ---
            'Student Management'    => [
                'Manage Student'         => 'View student records',
            ],
            'Student' => [
                'Student'         => 'For Student Route',
            ],

            // --- INSTRUCTOR TOOLS ---
            'Instructor Management' => [
                'Instructor'   => 'Access to class score attendent',
            ],

            // --- FINANCIALS ---
            'Financials' => [
                'Manage Payments'   => 'Manage cash flow',
            ],

            // --- SYSTEM ---
            'System' => [
                'Manage System' => 'View, Edit system settings',
            ],
        ];
    }

    /**
     * Centralized configuration for roles and their permission assignments.
     *
     * @return array
     */
    private function getRolesConfig(array $permissionsConfig): array
    {
        // Helper to flatten all permission names into a single array
        $allPermissions = collect($permissionsConfig)->flatMap(fn($permissions) => array_keys($permissions))->all();

        return [
            'Super Administrator' => [
                'description' => 'Full system access',
                'permissions' => [
                    'Manage Academic Structure',
                    'Administering',
                    'Manage Payments',
                    'Manage Operation',
                    'Manage Roles & Permissions',
                    'Manage System',
                    'Manage Users',
                ],
            ],
            'admin' => [
                'description' => 'System administrator',
                'permissions' => [
                    'Administering',
                    'Manage Academic Structure',
                    'Manage Payments',
                    'Manage Operation',
                    'Manage Roles & Permissions',
                    'Manage System',
                    'Manage Users',
                ],
            ],
            'register' => [
                'description' => 'Registrar office staff',
                'permissions' => [
                    'Administering',
                    'Manage Payments',
                    'Manage Users',
                ],
            ],
            'hod' => [
                'description' => 'Head of Department',
                'permissions' => [
                    'Manage Academic Structure',
                    'Administering',
                    'Manage Payments',
                    'Manage Operation',
                    'Manage System',
                    'Manage Users',
                ],
            ],
            'instructor' => [
                'description' => 'Teaching faculty',
                'permissions' => [
                    'Instructors',
                ],
            ],
            'staff'               => [
                'description' => 'Administrative staff',
                'permissions' => [
                    'Manage Users',
                ],
            ],
            'student'             => [
                'description' => 'Student access',
                'permissions' => [
                    'Student',
                ],
            ],
        ];
    }
}
