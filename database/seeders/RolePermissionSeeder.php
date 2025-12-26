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
            'User Management'       => [
                'view.users'   => 'View user accounts',
                'create.users' => 'Create new user accounts',
                'edit.users'   => 'Edit user accounts',
                'delete.users' => 'Delete user accounts',
            ],
            'Role Management'       => [
                'view.roles'   => 'View roles',
                'create.roles' => 'Create new roles',
                'edit.roles'   => 'Edit roles',
                'delete.roles' => 'Delete roles',
            ],

            // --- ACADEMIC STRUCTURE ---
            'Academic Structure'    => [
                'view.faculties'    => 'View faculties',
                'manage.structure'  => 'Manage structure (Faculties/Depts/Majors)',
                'view.departments'  => 'View departments',
                'view.programs'     => 'View programs',
                'manage.curriculum' => 'Manage curriculum (Roadmaps)',
            ],
            'Course Management'     => [
                'view.courses'   => 'View course catalog',
                'create.courses' => 'Create new courses',
                'edit.courses'   => 'Edit courses',
                'delete.courses' => 'Delete courses',
            ],
            'Scheduling'            => [
                'view.schedule'   => 'View class schedules',
                'manage.schedule' => 'Create/Edit class sessions',
                'batch.enroll'    => 'Run batch enrollment tool',
            ],

            // --- PEOPLE ---
            'Student Management'    => [
                'view.students'         => 'View student records',
                'create.students'       => 'Create student profiles',
                'edit.students'         => 'Edit student profiles',
                'delete.students'       => 'Soft delete students',
                'restore.students'      => 'Restore deleted students',
                'force-delete.students' => 'Permanently delete students',
            ],
            'Instructor Management' => [
                'view.instructors'   => 'View instructor list',
                'create.instructors' => 'Create instructor profiles',
                'edit.instructors'   => 'Edit instructor profiles',
                'delete.instructors' => 'Delete instructors',
            ],

            // --- INSTRUCTOR TOOLS ---
            'Grading'               => [
                'view.gradebook'  => 'Access gradebook',
                'submit.grades'   => 'Submit final grades',
                'view.attendance' => 'View attendance records',
                'take.attendance' => 'Take daily attendance',
            ],

            // --- FINANCIALS ---
            'Financials'            => [
                'view.invoices'   => 'View invoices',
                'create.invoices' => 'Generate invoices',
                'record.payments' => 'Record payments manually',
                'view.payments'   => 'View payment history',
            ],

            // --- SYSTEM ---
            'System'                => [
                'view.audit-logs'     => 'View audit logs',
                'view.system-configs' => 'View system settings',
                'edit.system-configs' => 'Edit system settings',
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
                'permissions' => $allPermissions,
            ],
            'admin'               => [
                'description' => 'System administrator',
                'permissions' => [
                    'view.users', 'create.users', 'edit.users',
                    'manage.structure', 'manage.curriculum',
                    'view.courses', 'create.courses', 'edit.courses',
                    'view.schedule', 'manage.schedule', 'batch.enroll',
                    'view.students', 'create.students', 'edit.students', 'delete.students', 'restore.students',
                    'view.instructors', 'create.instructors', 'edit.instructors',
                    'view.invoices', 'record.payments', 'view.payments',
                ],
            ],
            'register'            => [
                'description' => 'Registrar office staff',
                'permissions' => [
                    'view.users', 'create.users', 'edit.users',
                    'manage.structure', 'manage.curriculum',
                    'view.courses', 'create.courses', 'edit.courses',
                    'view.schedule', 'manage.schedule', 'batch.enroll',
                    'view.students', 'create.students', 'edit.students', 'delete.students', 'restore.students',
                    'view.instructors', 'create.instructors', 'edit.instructors',
                    'view.invoices', 'record.payments', 'view.payments',
                ],
            ],
            'hod'                 => [
                'description' => 'Head of Department',
                'permissions' => [
                    'view.users', 'create.users', 'edit.users',
                    'manage.structure', 'manage.curriculum',
                    'view.courses', 'create.courses', 'edit.courses',
                    'view.schedule', 'manage.schedule', 'batch.enroll',
                    'view.students', 'create.students', 'edit.students', 'delete.students', 'restore.students',
                    'view.instructors', 'create.instructors', 'edit.instructors',
                    'view.invoices', 'record.payments', 'view.payments',
                ],
            ],
            'professor'           => [
                'description' => 'Teaching faculty',
                'permissions' => [
                    'view.schedule',
                    'view.gradebook', 'submit.grades',
                    'view.attendance', 'take.attendance',
                    'view.students',
                ],
            ],
            'staff'               => [
                'description' => 'Administrative staff',
                'permissions' => [
                    'view.schedule',
                    'view.gradebook', 'submit.grades',
                    'view.attendance', 'take.attendance',
                    'view.students',
                ],
            ],
            'student'             => [
                'description' => 'Student access',
                'permissions' => [
                    'view.students',
                ],
            ],
        ];
    }
}
