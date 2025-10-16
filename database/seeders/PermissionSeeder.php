<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.impersonate',

            // Role Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign',

            // Department Management
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
            'departments.manage',

            // Student Management
            'students.view',
            'students.create',
            'students.edit',
            'students.delete',
            'students.manage_records',

            // Academic Management
            'programs.manage',
            'courses.manage',
            'syllabus.manage',
            'classes.manage',

            // Financial Management
            'fees.manage',
            'payments.view',
            'payments.manage',
            'scholarships.manage',

            // System Management
            'system.config',
            'backup.manage',
            'reports.view',
            'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name'        => $permission,
                'group'       => $this->getPermissionGroup($permission),
                'description' => $this->getPermissionDescription($permission),
            ]);
        }

        // Create roles and assign permissions
        $superAdmin = SpatieRole::create([
            'name'           => 'super_admin',
            'description'    => 'Full system access',
            'is_system_role' => true,
        ]);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = SpatieRole::create([
            'name'           => 'admin',
            'description'    => 'System administrator',
            'is_system_role' => true,
        ]);
        $admin->givePermissionTo([
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
            'students.view',
            'students.create',
            'students.edit',
            'students.delete',
            'programs.manage',
            'courses.manage',
            'fees.manage',
            'payments.view',
            'payments.manage',
            'reports.view',
        ]);

        $registrar = SpatieRole::create([
            'name'           => 'registrar',
            'description'    => 'Registrar office staff',
            'is_system_role' => true,

        ]);
        $registrar->givePermissionTo([
            'students.view',
            'students.create',
            'students.edit',
            'students.manage_records',
            'programs.manage',
            'courses.manage',
            'reports.view',
        ]);

        $hod = SpatieRole::create([
            'name'           => 'hod',
            'description'    => 'Head of Department',
            'is_system_role' => true,
        ]);
        $hod->givePermissionTo([
            'departments.manage',
            'students.view',
            'students.manage_records',
            'programs.manage',
            'courses.manage',
            'syllabus.manage',
        ]);

        $professor = SpatieRole::create([
            'name'           => 'professor',
            'description'    => 'Teaching faculty',
            'is_system_role' => true,
        ]);
        $professor->givePermissionTo([
            'students.view',
            'courses.manage',
            'classes.manage',
        ]);

        $staff = SpatieRole::create([
            'name'           => 'staff',
            'description'    => 'Administrative staff',
            'is_system_role' => true,
        ]);
        $staff->givePermissionTo([
            'students.view',
            'payments.view',
        ]);

        $student = SpatieRole::create([
            'name'           => 'student',
            'description'    => 'Student access',
            'is_system_role' => true,
        ]);
        $student->givePermissionTo([
            // Limited permissions for students
        ]);

        // Create super admin user
        $user = \App\Models\User::factory()->create([
            'name'     => 'Super Administrator',
            'username' => 'superadmin',
            'email'    => 'superadmin@university.edu',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('super_admin');
    }

    private function getPermissionGroup($permission): string
    {
        if (str_starts_with($permission, 'users.')) {
            return 'User Management';
        }

        if (str_starts_with($permission, 'roles.')) {
            return 'Role Management';
        }

        if (str_starts_with($permission, 'departments.')) {
            return 'Department Management';
        }

        if (str_starts_with($permission, 'students.')) {
            return 'Student Management';
        }

        if (
            str_starts_with($permission, 'programs.')
            || str_starts_with($permission, 'courses.')
            || str_starts_with($permission, 'syllabus.')
            || str_starts_with($permission, 'classes.')
        ) {
            return 'Academic Management';
        }

        if (
            str_starts_with($permission, 'fees.')
            || str_starts_with($permission, 'payments.')
            || str_starts_with($permission, 'scholarships.')
        ) {
            return 'Financial Management';
        }

        if (
            str_starts_with($permission, 'system.')
            || str_starts_with($permission, 'backup.')
            || str_starts_with($permission, 'reports.')
            || str_starts_with($permission, 'audit.')
        ) {
            return 'System Management';
        }

        return 'General';
    }

    private function getPermissionDescription($permission): string
    {
        $descriptions = [
            'users.view' => 'View user accounts',
            'users.create' => 'Create new user accounts',
            'users.edit' => 'Edit existing user accounts',
            'users.delete' => 'Delete user accounts',
            'users.impersonate' => 'Impersonate other users',
            'departments.view' => 'View departments',
            'departments.create' => 'Create new departments',
            'departments.edit' => 'Edit departments',
            'departments.delete' => 'Delete departments',
            'students.view' => 'View student records',
            'students.create' => 'Create new student records',
            'students.edit' => 'Edit student records',
            'students.delete' => 'Delete student records',
            // Add more descriptions as needed
        ];

        return $descriptions[$permission] ?? 'No description available';
    }
}
