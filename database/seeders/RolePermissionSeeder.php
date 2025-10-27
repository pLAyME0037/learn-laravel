<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use \Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]
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
            'academic_years.view',
            'academic_years.create',
            'academic_years.edit',
            'academic_years.delete',
            'academic_years.manage',
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
        $superUser = SpatieRole::create([
            'name'           => 'Super Administrator',
            'description'    => 'Full system access',
            'is_system_role' => true,
        ]);
        $superUser->givePermissionTo($permissions);

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
            'users.impersonate',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign',
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
            'departments.manage',
            'students.view',
            'students.create',
            'students.edit',
            'students.delete',
            'students.manage_records',
            'academic_years.view',
            'academic_years.create',
            'academic_years.edit',
            'academic_years.delete',
            'academic_years.manage',
            'programs.manage',
            'courses.manage',
            'syllabus.manage',
            'classes.manage',
            'fees.manage',
            'payments.view',
            'payments.manage',
            'scholarships.manage',
            'system.config',
            'backup.manage',
            'reports.view',
            'audit.view',
        ]);

        $registrar = SpatieRole::create([
            'name'           => 'register',
            'description'    => 'Register office staff',
            'is_system_role' => true,

        ]);
        $registrar->givePermissionTo([
            'students.view',
            'students.create',
            'students.edit',
            'students.manage_records',
            'programs.manage',
            'courses.manage',
            'syllabus.manage',
            'classes.manage',
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
            'classes.manage',
        ]);

        $professor = SpatieRole::create([
            'name'           => 'professor',
            'description'    => 'Teaching faculty',
            'is_system_role' => true,
        ]);
        $professor->givePermissionTo([
            'students.view',
            'courses.manage',
            'syllabus.manage',
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
        $user = new \App\Models\User();
        $user->name = 'Super Administrator';
        $user->username = 'superuser';
        $user->email = 'superuser@example.com';
        $user->password = bcrypt('password');
        $user->save();

        // Assign the Spatie role 'Super Administrator' directly using the Spatie Role model
        // Ensure the Spatie role 'Super Administrator' exists before assigning
        $superUserSpatieRole = SpatieRole::findByName('Super Administrator');
        if ($superUserSpatieRole) {
            // Directly attach the role to the user's roles relationship to bypass custom assignRole method
            $user->roles()->attach($superUserSpatieRole->id);
        }
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
            'students.manage_records' => 'Manage student academic records',

            'roles.view' => 'View roles',
            'roles.create' => 'Create new roles',
            'roles.edit' => 'Edit existing roles',
            'roles.delete' => 'Delete roles',
            'roles.assign' => 'Assign roles to users',

            'departments.manage' => 'Manage department settings and data',

            'programs.manage' => 'Manage academic programs',
            'courses.manage' => 'Manage courses',
            'syllabus.manage' => 'Manage syllabus for courses',
            'classes.manage' => 'Manage classes and schedules',

            'fees.manage' => 'Manage student fees',
            'payments.view' => 'View payment records',
            'payments.manage' => 'Manage student payments',
            'scholarships.manage' => 'Manage scholarships',

            'system.config' => 'Access and modify system configurations',
            'backup.manage' => 'Manage system backups',
            'reports.view' => 'View system reports',
            'audit.view' => 'View audit logs',
        ];

        return $descriptions[$permission] ?? 'No description available';
    }
}
