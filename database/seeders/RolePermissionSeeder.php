<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use \Spatie\Permission\PermissionRegistrar;

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
            'users.edit-access',
            'users.update-access',
            'users.status',
            'users.restore',
            'users.force-delete',

            // Role Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.assign',
            'roles.edit-permissions',
            'roles.update-permissions',

            // Department Management
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
            'departments.restore',
            'departments.force-delete',
            'departments.manage', // Keep for broader access if needed

            // Student Management
            'students.view',
            'students.create',
            'students.edit',
            'students.delete',
            'students.restore',
            'students.force-delete',
            'students.status',
            'students.manage_records',

            // System Configuration
            'system-configs.view',
            'system-configs.create',
            'system-configs.edit',
            'system-configs.delete',
            'system.config', // Keep for broader access if needed

            // Academic Year Management
            'academic-years.view',
            'academic-years.create',
            'academic-years.edit',
            'academic-years.delete',
            'academic-years.manage', // Keep for broader access if needed
            'syllabus.manage', // Re-added
            'classes.manage', // Re-added

            // Academic Record Management
            'academic-records.view',
            'academic-records.create',
            'academic-records.edit',
            'academic-records.delete',

            // Attendance Management
            'attendances.view',
            'attendances.create',
            'attendances.edit',
            'attendances.delete',

            // Audit Log Management
            'audit-logs.view',
            'audit-logs.create',
            'audit-logs.edit',
            'audit-logs.delete',
            'audit.view', // Keep for broader access if needed

            // Classroom Management
            'classrooms.view',
            'classrooms.create',
            'classrooms.edit',
            'classrooms.delete',

            // Class Schedule Management
            'class-schedules.view',
            'class-schedules.create',
            'class-schedules.edit',
            'class-schedules.delete',

            // Contact Detail Management
            'contact-details.view',
            'contact-details.create',
            'contact-details.edit',
            'contact-details.delete',

            // Course Management
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',
            'courses.manage', // Keep for broader access if needed

            // Course Prerequisite Management
            'course-prerequisites.view',
            'course-prerequisites.create',
            'course-prerequisites.edit',
            'course-prerequisites.delete',

            // Credit Score Management
            'credit-scores.view',
            'credit-scores.create',
            'credit-scores.edit',
            'credit-scores.delete',

            // Degree Management
            'degrees.view',
            'degrees.create',
            'degrees.edit',
            'degrees.delete',

            // Enrollment Management
            'enrollments.view',
            'enrollments.create',
            'enrollments.edit',
            'enrollments.delete',

            // Faculty Management
            'faculties.view',
            'faculties.create',
            'faculties.edit',
            'faculties.delete',

            // Gender Management
            'genders.view',
            'genders.create',
            'genders.edit',
            'genders.delete',

            // Instructor Management
            'instructors.view',
            'instructors.create',
            'instructors.edit',
            'instructors.delete',

            // Major Management
            'majors.view',
            'majors.create',
            'majors.edit',
            'majors.delete',

            // Financial Management
            'fees.manage',
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'payments.manage', // Keep for broader access if needed
            'scholarships.manage',

            // Program Management
            'programs.view',
            'programs.create',
            'programs.edit',
            'programs.delete',
            'programs.manage', // Keep for broader access if needed

            // Semester Management
            'semesters.view',
            'semesters.create',
            'semesters.edit',
            'semesters.delete',

            // Transaction Ledger Management
            'transaction-ledgers.view',
            'transaction-ledgers.create',
            'transaction-ledgers.edit',
            'transaction-ledgers.delete',

            // Permission Management
            'permissions.view',

            // Notification
            'send-notification',

            // Backup & Recovery
            'backups.view',
            'backups.create',
            'backups.download',
            'backups.restore',
            'backups.delete',
            'backup.manage', // Keep for broader access if needed

            // Login History
            'login-histories.view',

            // Reports
            'reports.view',
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
        // For now, give admin all permissions to resolve the seeding error.
        // These can be refined later if a specific subset is desired.
        $admin->givePermissionTo($permissions);

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
            'programs.view',
            'programs.create',
            'programs.edit',
            'programs.delete',
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',
            'syllabus.manage',
            'classes.manage',
            'reports.view',
            'semesters.view',
            'semesters.create',
            'semesters.edit',
            'semesters.delete',
            'enrollments.view',
            'enrollments.create',
            'enrollments.edit',
            'enrollments.delete',
            'academic-years.view',
            'academic-years.create',
            'academic-years.edit',
            'academic-years.delete',
            'academic-records.view',
            'academic-records.create',
            'academic-records.edit',
            'academic-records.delete',
            'class-schedules.view',
            'class-schedules.create',
            'class-schedules.edit',
            'class-schedules.delete',
            'degrees.view',
            'degrees.create',
            'degrees.edit',
            'degrees.delete',
            'majors.view',
            'majors.create',
            'majors.edit',
            'majors.delete',
            'genders.view',
            'genders.create',
            'genders.edit',
            'genders.delete',
            'contact-details.view',
            'contact-details.create',
            'contact-details.edit',
            'contact-details.delete',
        ]);

        $hod = SpatieRole::create([
            'name'           => 'hod',
            'description'    => 'Head of Department',
            'is_system_role' => true,
        ]);
        $hod->givePermissionTo([
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
            'departments.manage', // Keep for broader access if needed
            'students.view',
            'students.manage_records',
            'programs.view',
            'programs.create',
            'programs.edit',
            'programs.delete',
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',
            'syllabus.manage',
            'classes.manage',
            'academic-years.view',
            'academic-records.view',
            'class-schedules.view',
            'instructors.view',
            'semesters.view',
        ]);

        $professor = SpatieRole::create([
            'name'           => 'professor',
            'description'    => 'Teaching faculty',
            'is_system_role' => true,
        ]);
        $professor->givePermissionTo([
            'students.view',
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.delete',
            'syllabus.manage',
            'classes.manage',
            'academic-records.view',
            'academic-records.create',
            'academic-records.edit',
            'academic-records.delete',
            'attendances.view',
            'attendances.create',
            'attendances.edit',
            'attendances.delete',
            'class-schedules.view',
            'class-schedules.create',
            'class-schedules.edit',
            'class-schedules.delete',
            'enrollments.view',
            'enrollments.create',
            'enrollments.edit',
            'enrollments.delete',
            // Assuming a 'grades.manage' permission for professors, if applicable
            // 'grades.manage',
        ]);

        $staff = SpatieRole::create([
            'name'           => 'staff',
            'description'    => 'Administrative staff',
            'is_system_role' => true,
        ]);
        $staff->givePermissionTo([
            'students.view',
            'payments.view',
            'payments.create',
            'payments.edit',
            'payments.delete',
            'academic-records.view',
            'attendances.view',
            'classrooms.view',
            'class-schedules.view',
            'contact-details.view',
            'courses.view',
            'degrees.view',
            'enrollments.view',
            'faculties.view',
            'genders.view',
            'instructors.view',
            'majors.view',
            'programs.view',
            'semesters.view',
            'transaction-ledgers.view',
            'audit-logs.view',
            'login-histories.view',
            'reports.view',
            'system-configs.view',
            'academic-years.view',
        ]);

        $student = SpatieRole::create([
            'name'           => 'student',
            'description'    => 'Student access',
            'is_system_role' => true,
        ]);
        $student->givePermissionTo([
            'academic-records.view',
            'attendances.view',
            'courses.view',
            'enrollments.view',
            'payments.view',
            'semesters.view',
            'academic-years.view',
            'class-schedules.view',
            // Assuming a 'grades.view' permission for students, if applicable
            // 'grades.view',
        ]);

        // Create super admin user
        $user           = new \App\Models\User();
        $user->name     = 'Super Administrator';
        $user->username = 'superuser';
        $user->email    = 'superuser@example.com';
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
        if (str_starts_with($permission, 'system-configs.')) {
            return 'System Configuration';
        }
        if (str_starts_with($permission, 'academic-years.')) {
            return 'Academic Year Management';
        }
        if (str_starts_with($permission, 'academic-records.')) {
            return 'Academic Record Management';
        }
        if (str_starts_with($permission, 'attendances.')) {
            return 'Attendance Management';
        }
        if (str_starts_with($permission, 'audit-logs.')) {
            return 'Audit Log Management';
        }
        if (str_starts_with($permission, 'classrooms.')) {
            return 'Classroom Management';
        }
        if (str_starts_with($permission, 'class-schedules.')) {
            return 'Class Schedule Management';
        }
        if (str_starts_with($permission, 'contact-details.')) {
            return 'Contact Detail Management';
        }
        if (str_starts_with($permission, 'courses.')) {
            return 'Course Management';
        }
        if (str_starts_with($permission, 'course-prerequisites.')) {
            return 'Course Prerequisite Management';
        }
        if (str_starts_with($permission, 'credit-scores.')) {
            return 'Credit Score Management';
        }
        if (str_starts_with($permission, 'degrees.')) {
            return 'Degree Management';
        }
        if (str_starts_with($permission, 'enrollments.')) {
            return 'Enrollment Management';
        }
        if (str_starts_with($permission, 'faculties.')) {
            return 'Faculty Management';
        }
        if (str_starts_with($permission, 'genders.')) {
            return 'Gender Management';
        }
        if (str_starts_with($permission, 'instructors.')) {
            return 'Instructor Management';
        }
        if (str_starts_with($permission, 'majors.')) {
            return 'Major Management';
        }
        if (str_starts_with($permission, 'payments.')) {
            return 'Payment Management';
        }
        if (str_starts_with($permission, 'programs.')) {
            return 'Program Management';
        }
        if (str_starts_with($permission, 'semesters.')) {
            return 'Semester Management';
        }
        if (str_starts_with($permission, 'transaction-ledgers.')) {
            return 'Transaction Ledger Management';
        }
        if (str_starts_with($permission, 'permissions.')) {
            return 'Permission Management';
        }
        if (str_starts_with($permission, 'send-notification')) {
            return 'Notification';
        }
        if (str_starts_with($permission, 'backups.')) {
            return 'Backup & Recovery';
        }
        if (str_starts_with($permission, 'login-histories.')) {
            return 'Login History';
        }
        if (str_starts_with($permission, 'reports.')) {
            return 'Reports';
        }
        if (str_starts_with($permission, 'audit.')) {
            return 'Audit Management';
        }
        if (str_starts_with($permission, 'system.')) {
            return 'System Management';
        }
        if (str_starts_with($permission, 'fees.') || str_starts_with($permission, 'scholarships.')) {
            return 'Financial Management';
        }
        if (str_starts_with($permission, 'syllabus.') || str_starts_with($permission, 'classes.')) {
            return 'Academic Management';
        }

        return 'General';
    }

    private function getPermissionDescription($permission): string
    {
        $descriptions = [
            // User Management
            'users.view' => 'View user accounts',
            'users.create' => 'Create new user accounts',
            'users.edit' => 'Edit existing user accounts',
            'users.delete' => 'Delete user accounts',
            'users.impersonate' => 'Impersonate other users',
            'users.edit-access' => 'Edit user access permissions',
            'users.update-access' => 'Update user access permissions',
            'users.status' => 'Update user account status',
            'users.restore' => 'Restore soft-deleted user accounts',
            'users.force-delete' => 'Permanently delete user accounts',

            // Role Management
            'roles.view' => 'View roles',
            'roles.create' => 'Create new roles',
            'roles.edit' => 'Edit existing roles',
            'roles.delete' => 'Delete roles',
            'roles.assign' => 'Assign roles to users',
            'roles.edit-permissions' => 'Edit permissions assigned to roles',
            'roles.update-permissions' => 'Update permissions assigned to roles',

            // Department Management
            'departments.view' => 'View departments',
            'departments.create' => 'Create new departments',
            'departments.edit' => 'Edit departments',
            'departments.delete' => 'Delete departments',
            'departments.restore' => 'Restore soft-deleted departments',
            'departments.force-delete' => 'Permanently delete departments',
            'departments.manage' => 'Manage department settings and data',

            // Student Management
            'students.view' => 'View student records',
            'students.create' => 'Create new student records',
            'students.edit' => 'Edit student records',
            'students.delete' => 'Delete student records',
            'students.restore' => 'Restore soft-deleted student records',
            'students.force-delete' => 'Permanently delete student records',
            'students.status' => 'Update student account status',
            'students.manage_records' => 'Manage student academic records',

            // System Configuration
            'system-configs.view' => 'View system configurations',
            'system-configs.create' => 'Create new system configurations',
            'system-configs.edit' => 'Edit existing system configurations',
            'system-configs.delete' => 'Delete system configurations',
            'system.config' => 'Access and modify system configurations (broad)',

            // Academic Year Management
            'academic-years.view' => 'View academic years',
            'academic-years.create' => 'Create new academic years',
            'academic-years.edit' => 'Edit existing academic years',
            'academic-years.delete' => 'Delete academic years',
            'academic-years.manage' => 'Manage academic year settings and data',

            // Academic Record Management
            'academic-records.view' => 'View academic records',
            'academic-records.create' => 'Create new academic records',
            'academic-records.edit' => 'Edit existing academic records',
            'academic-records.delete' => 'Delete academic records',

            // Attendance Management
            'attendances.view' => 'View attendance records',
            'attendances.create' => 'Create new attendance records',
            'attendances.edit' => 'Edit existing attendance records',
            'attendances.delete' => 'Delete attendance records',

            // Audit Log Management
            'audit-logs.view' => 'View audit logs',
            'audit-logs.create' => 'Create new audit logs', // Though typically auto-generated
            'audit-logs.edit' => 'Edit audit logs', // Though typically not editable
            'audit-logs.delete' => 'Delete audit logs',
            'audit.view' => 'View audit logs (broad)',

            // Classroom Management
            'classrooms.view' => 'View classrooms',
            'classrooms.create' => 'Create new classrooms',
            'classrooms.edit' => 'Edit existing classrooms',
            'classrooms.delete' => 'Delete classrooms',

            // Class Schedule Management
            'class-schedules.view' => 'View class schedules',
            'class-schedules.create' => 'Create new class schedules',
            'class-schedules.edit' => 'Edit existing class schedules',
            'class-schedules.delete' => 'Delete class schedules',

            // Contact Detail Management
            'contact-details.view' => 'View contact details',
            'contact-details.create' => 'Create new contact details',
            'contact-details.edit' => 'Edit existing contact details',
            'contact-details.delete' => 'Delete contact details',

            // Course Management
            'courses.view' => 'View courses',
            'courses.create' => 'Create new courses',
            'courses.edit' => 'Edit existing courses',
            'courses.delete' => 'Delete courses',
            'courses.manage' => 'Manage courses (broad)',

            // Course Prerequisite Management
            'course-prerequisites.view' => 'View course prerequisites',
            'course-prerequisites.create' => 'Create new course prerequisites',
            'course-prerequisites.edit' => 'Edit existing course prerequisites',
            'course-prerequisites.delete' => 'Delete course prerequisites',

            // Credit Score Management
            'credit-scores.view' => 'View credit scores',
            'credit-scores.create' => 'Create new credit scores',
            'credit-scores.edit' => 'Edit existing credit scores',
            'credit-scores.delete' => 'Delete credit scores',

            // Degree Management
            'degrees.view' => 'View degrees',
            'degrees.create' => 'Create new degrees',
            'degrees.edit' => 'Edit existing degrees',
            'degrees.delete' => 'Delete degrees',

            // Enrollment Management
            'enrollments.view' => 'View enrollments',
            'enrollments.create' => 'Create new enrollments',
            'enrollments.edit' => 'Edit existing enrollments',
            'enrollments.delete' => 'Delete enrollments',

            // Faculty Management
            'faculties.view' => 'View faculties',
            'faculties.create' => 'Create new faculties',
            'faculties.edit' => 'Edit existing faculties',
            'faculties.delete' => 'Delete faculties',

            // Gender Management
            'genders.view' => 'View genders',
            'genders.create' => 'Create new genders',
            'genders.edit' => 'Edit existing genders',
            'genders.delete' => 'Delete genders',

            // Instructor Management
            'instructors.view' => 'View instructors',
            'instructors.create' => 'Create new instructors',
            'instructors.edit' => 'Edit existing instructors',
            'instructors.delete' => 'Delete instructors',

            // Major Management
            'majors.view' => 'View majors',
            'majors.create' => 'Create new majors',
            'majors.edit' => 'Edit existing majors',
            'majors.delete' => 'Delete majors',

            // Financial Management
            'fees.manage' => 'Manage student fees',
            'payments.view' => 'View payment records',
            'payments.create' => 'Create new payment records',
            'payments.edit' => 'Edit existing payment records',
            'payments.delete' => 'Delete payment records',
            'payments.manage' => 'Manage student payments (broad)',
            'scholarships.manage' => 'Manage scholarships',

            // Program Management
            'programs.view' => 'View academic programs',
            'programs.create' => 'Create new academic programs',
            'programs.edit' => 'Edit existing academic programs',
            'programs.delete' => 'Delete academic programs',
            'programs.manage' => 'Manage academic programs (broad)',

            // Semester Management
            'semesters.view' => 'View semesters',
            'semesters.create' => 'Create new semesters',
            'semesters.edit' => 'Edit existing semesters',
            'semesters.delete' => 'Delete semesters',

            // Transaction Ledger Management
            'transaction-ledgers.view' => 'View transaction ledger entries',
            'transaction-ledgers.create' => 'Create new transaction ledger entries',
            'transaction-ledgers.edit' => 'Edit existing transaction ledger entries',
            'transaction-ledgers.delete' => 'Delete transaction ledger entries',

            // Permission Management
            'permissions.view' => 'View permissions',

            // Notification
            'send-notification' => 'Send general notifications',

            // Backup & Recovery
            'backups.view' => 'View backup records',
            'backups.create' => 'Create new system backups',
            'backups.download' => 'Download system backups',
            'backups.restore' => 'Restore system from backup',
            'backups.delete' => 'Delete system backups',
            'backup.manage' => 'Manage system backups (broad)',

            // Login History
            'login-histories.view' => 'View login history records',

            // Reports
            'reports.view' => 'View system reports',
        ];

        return $descriptions[$permission] ?? 'No description available';
    }
}
