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

        // 1. Define all permissions in a structured, maintainable way
        $permissionsConfig = $this->getPermissionsConfig();

        // 2. Seed all permissions in a single batch operation
        $this->seedPermissions($permissionsConfig);

        // 3. Define all roles and their associated permissions
        $rolesConfig = $this->getRolesConfig($permissionsConfig);

        // 4. Seed roles and assign permissions efficiently
        $this->seedRolesAndAssignPermissions($rolesConfig);

        // 5. Create the super administrator user and assign the role
        $this->seedSuperAdminUser();

        $this->command->info('Roles and permissions have been seeded successfully.');
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
            'User Management'      => [
                'view.users'         => 'View user accounts',
                'create.users'       => 'Create new user accounts',
                'edit.users'         => 'Edit user accounts',
                'delete.users'       => 'Delete user accounts',
                'impersonate.users'  => 'Impersonate other users',
                'edit-access.users'  => 'Edit user access permissions',
                'status.users'       => 'Update user account status',
                'restore.users'      => 'Restore soft-deleted users',
                'force-delete.users' => 'Permanently delete users',
            ],
            'Role Management'      => [
                'view.roles'             => 'View roles',
                'create.roles'           => 'Create new roles',
                'edit.roles'             => 'Edit roles',
                'delete.roles'           => 'Delete roles',
                'assign.roles'           => 'Assign roles to users',
                'edit-permissions.roles' => 'Edit permissions for roles',
            ],
            'Student Management'   => [
                'view.students'           => 'View student records',
                'create.students'         => 'Create new student records',
                'edit.students'           => 'Edit student records',
                'delete.students'         => 'Delete student records',
                'restore.students'        => 'Restore soft-deleted students',
                'force-delete.students'   => 'Permanently delete students',
                'status.students'         => 'Update student account status',
                'manage_records.students' => 'Manage student academic records',
            ],
            'Department Management' => [
                'view.departments'   => 'View departments',
                'create.departments' => 'Create new departments',
                'edit.departments'   => 'Edit existing departments',
                'delete.departments' => 'Delete departments',
                'manage.departments' => 'Manage department settings',
            ],
            'Faculty Management' => [
                'view.faculties'   => 'View faculties',
                'create.faculties' => 'Create new faculties',
                'edit.faculties'   => 'Edit existing faculties',
                'delete.faculties' => 'Delete faculties',
            ],
            'Major Management' => [
                'view.majors'   => 'View majors',
                'create.majors' => 'Create new majors',
                'edit.majors'   => 'Edit existing majors',
                'delete.majors' => 'Delete majors',
            ],
            'Program Management'   => [
                'view.programs'   => 'View academic programs',
                'create.programs' => 'Create new academic programs',
                'edit.programs'   => 'Edit existing academic programs',
                'delete.programs' => 'Delete academic programs',
            ],
            'Enrollment Management' => [
                'view.enrollments'   => 'View student enrollments',
                'create.enrollments' => 'Create new student enrollments',
                'edit.enrollments'   => 'Edit existing student enrollments',
                'delete.enrollments' => 'Delete student enrollments',
            ],
            'Academic Record Management' => [
                'view.academic-records'   => 'View academic records',
                'create.academic-records' => 'Create new academic records',
                'edit.academic-records'   => 'Edit existing academic records',
                'delete.academic-records' => 'Delete academic records',
            ],            
            'Class Management' => [
                'view.classes'   => 'View classes',
                'create.classes' => 'Create new classes',
                'edit.classes'   => 'Edit existing classes',
                'delete.classes' => 'Delete classes',
                'manage.classes' => 'Manage class profiles',
            ],              
            'Classroom Management' => [
                'view.classrooms'   => 'View classrooms',
                'create.classrooms' => 'Create new classrooms',
                'edit.classrooms'   => 'Edit existing classrooms',
                'delete.classrooms' => 'Delete classrooms',
                'manage.classrooms' => 'Manage class profiles',
            ],            
            'Attendance Management' => [
                'view.attendances'   => 'View attendances',
                'create.attendances' => 'Create new attendances',
                'edit.attendances'   => 'Edit existing attendances',
                'delete.attendances' => 'Delete attendances',
                'manage.attendances' => 'Manage attendance profiles',
            ],
            'Academic Year Management' => [
                'view.academic_years'   => 'View academic_years',
                'create.academic_years' => 'Create new academic_years',
                'edit.academic_years'   => 'Edit existing academic_years',
                'delete.academic_years' => 'Delete academic_years',
                'manage.academic_years' => 'Manage academic_years profiles',
            ],
            'Report Management' => [
                'view.reports'   => 'View reports',
                'create.reports' => 'Create new reports',
                'edit.reports'   => 'Edit existing reports',
                'delete.reports' => 'Delete reports',
                'manage.reports' => 'Manage report profiles',
            ],
            'Permission Management' => [
                'view.permissions'   => 'View permissions',
                'create.permissions' => 'Create new permissions',
                'edit.permissions'   => 'Edit existing permissions',
                'delete.permissions' => 'Delete permissions',
                'manage.permissions' => 'Manage all permissions (create, edit, delete)',
            ],
            'Syllabus Management'   => [
                'manage.syllabus' => 'Manage course syllabi',
            ],
            'Semester Management' => [
                'view.semesters'   => 'View semesters',
                'create.semesters' => 'Create new semesters',
                'edit.semesters'   => 'Edit existing semesters',
                'delete.semesters' => 'Delete semesters',
                'manage.semesters' => 'Manage semester profiles',
            ],
            'Instructor Management' => [
                'view.instructors'   => 'View instructors',
                'create.instructors' => 'Create new instructors',
                'edit.instructors'   => 'Edit existing instructors',
                'delete.instructors' => 'Delete instructors',
                'manage.instructors' => 'Manage instructor profiles',
            ],
            'Course Management'    => [
                'view.courses'   => 'View courses',
                'create.courses' => 'Create new courses',
                'edit.courses'   => 'Edit existing courses',
                'delete.courses' => 'Delete courses',
                'manage.courses' => 'Manage courses (broad)',
            ],
            'Course Prerequisites Management'    => [
                'view.course-prerequisites'   => 'View course-prerequisites',
                'create.course-prerequisites' => 'Create new course-prerequisites',
                'edit.course-prerequisites'   => 'Edit existing course-prerequisites',
                'delete.course-prerequisites' => 'Delete course-prerequisites',
            ],
            'Class Schdule Management'    => [
                'view.class-schedules'   => 'View class-schedules',
                'create.class-schedules' => 'Create new class-schedules',
                'edit.class-schedules'   => 'Edit existing class-schedules',
                'delete.class-schedules' => 'Delete class-schedules',
            ],
            'Contact Detail Management'    => [
                'view.contact-details'   => 'View contact-details',
                'create.contact-details' => 'Create new contact-details',
                'edit.contact-details'   => 'Edit existing contact-details',
                'delete.contact-details' => 'Delete contact-details',
            ],
            'Credit Score Management'    => [
                'view.credit-scores'   => 'View credit scores',
                'create.credit-scores' => 'Create new credit scores',
                'edit.credit-scores'   => 'Edit existing credit scores',
                'delete.credit-scores' => 'Delete credit scores',
            ],
            'Audit Log Management'    => [
                'view.audit_logs'   => 'View audit_logs',
                'create.audit_logs' => 'Create new audit_logs',
                'edit.audit_logs'   => 'Edit existing audit_logs',
                'delete.audit_logs' => 'Delete audit_logs',
            ],
            'Financial Management' => [
                'manage.fees'         => 'Manage student fees',
                'view.payments'       => 'View payment records',
                'create.payments'     => 'Create new payment records',
                'edit.payments'       => 'Edit existing payment records',
                'delete.payments'     => 'Delete payment records',
                'manage.scholarships' => 'Manage scholarships',
            ],
            'Degree Management' => [
                'view.degrees'   => 'View academic degrees',
                'create.degrees' => 'Create new academic degrees',
                'edit.degrees'   => 'Edit existing academic degrees',
                'delete.degrees' => 'Delete academic degrees',
            ],
            'System & Security'    => [
                'view.audit-logs'      => 'View audit logs',
                'view.login-histories' => 'View login history',
                'view.backups'         => 'View system backups',
                'create.backups'       => 'Create new system backups',
                'download.backups'     => 'Download system backups',
                'restore.backups'      => 'Restore system from backup',
                'delete.backups'       => 'Delete system backups',
                'view.system-configs'  => 'View system configurations',
                'edit.system-configs'  => 'Edit system configurations',
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
                'permissions' => $allPermissions, // Can be refined later
            ],
            'register'            => [
                'description' => 'Registrar office staff',
                'permissions' => [
                    'view.students', 'create.students', 'edit.students', 'manage_records.students',
                    'view.programs', 'create.programs', 'edit.programs', 'delete.programs',
                    'view.courses', 'create.courses', 'edit.courses', 'delete.courses',
                    'view.enrollments', 'create.enrollments', 'edit.enrollments',
                    'view.academic-records', 'create.academic-records', 'edit.academic-records',
                    'view.faculties', 'view.majors', 'view.degrees',
                    'view.class-schedules', 'view.classrooms', 'view.academic_years',
                    'view.semesters', 'view.payments', 'edit.payments', 'manage.fees',
                    'view.login-histories', 'view.audit-logs', 'view.system-configs',
                ],
            ],
            'hod'                 => [
                'description' => 'Head of Department',
                'permissions' => [
                    'view.departments', 'manage.departments', 'edit.departments',
                    'view.students', 'edit.students', 'manage_records.students',
                    'view.programs', 'edit.programs', 'view.courses', 'edit.courses',
                    'view.instructors', 'edit.instructors', 'manage.instructors',
                    'view.academic-records', 'edit.academic-records',
                    'view.attendances', 'edit.attendances', 'manage.attendances',
                    'view.class-schedules', 'edit.class-schedules',
                    'manage.syllabus', 'view.reports',
                ],
            ],
            'professor'           => [
                'description' => 'Teaching faculty',
                'permissions' => [
                    'view.students', 'view.courses', 'manage.syllabus', 'manage.classes', 'edit.classes',
                    'view.academic-records', 'create.academic-records', 'edit.academic-records',
                    'view.attendances', 'create.attendances', 'edit.attendances',
                    'view.class-schedules', 'create.class-schedules', 'edit.class-schedules',
                    'view.contact-details',
                ],
            ],
            'staff'               => [
                'description' => 'Administrative staff',
                'permissions' => [
                    'view.students', 'edit.students', 'view.payments', 'create.payments', 'edit.payments',
                    'view.reports', 'create.reports', 'view.enrollments', 'edit.enrollments',
                    'view.academic_years', 'view.semesters', 'view.faculties', 'view.departments',
                    'view.programs', 'view.courses', 'view.instructors', 'view.classrooms',
                    'manage.fees', 'manage.scholarships',
                    'view.contact-details', 'view.audit-logs', 'view.login-histories',
                ],
            ],
            'student'             => [
                'description' => 'Student access',
                'permissions' => [
                    'view.academic-records', 'view.attendances', 'view.courses',
                    'view.enrollments', 'view.payments', 'view.class-schedules',
                    'view.credit-scores', 'view.contact-details', 'view.reports',
                    'view.programs', 'view.departments', 'view.faculties', 'view.majors',
                    'view.degrees',
                ],
            ],
        ];
    }
}
