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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view.users',
            'create.users',
            'edit.users',
            'delete.users',
            'impersonate.users',
            'edit-access.users',
            'update-access.users',
            'status.users',
            'restore.users',
            'force-delete.users',

            // Role Management
            'view.roles',
            'create.roles',
            'edit.roles',
            'delete.roles',
            'assign.roles',
            'edit-permissions.roles',
            'update-permissions.roles',

            // Department Management
            'view.departments',
            'create.departments',
            'edit.departments',
            'delete.departments',
            'restore.departments',
            'force-delete.departments',
            'manage.departments',

            // Student Management
            'view.students',
            'create.students',
            'edit.students',
            'delete.students',
            'restore.students',
            'force-delete.students',
            'status.students',
            'manage_records.students',

            // System Configuration
            'view.system-configs',
            'create.system-configs',
            'edit.system-configs',
            'delete.system-configs',
            'system.config',

            // Academic Year Management
            'view.academic-years',
            'create.academic-years',
            'edit.academic-years',
            'delete.academic-years',
            'manage.academic-years',
            'manage.syllabus',
            'manage.classes',

            // Academic Record Management
            'view.academic-records',
            'create.academic-records',
            'edit.academic-records',
            'delete.academic-records',

            // Attendance Management
            'view.attendances',
            'create.attendances',
            'edit.attendances',
            'delete.attendances',

            // Audit Log Management
            'view.audit-logs',
            'create.audit-logs',
            'edit.audit-logs',
            'delete.audit-logs',

            // Classroom Management
            'view.classrooms',
            'create.classrooms',
            'edit.classrooms',
            'delete.classrooms',

            // Class Schedule Management
            'view.class-schedules',
            'create.class-schedules',
            'edit.class-schedules',
            'delete.class-schedules',

            // Contact Detail Management
            'view.contact-details',
            'create.contact-details',
            'edit.contact-details',
            'delete.contact-details',

            // Course Management
            'view.courses',
            'create.courses',
            'edit.courses',
            'delete.courses',
            'manage.courses',

            // Course Prerequisite Management
            'view.course-prerequisites',
            'create.course-prerequisites',
            'edit.course-prerequisites',
            'delete.course-prerequisites',

            // Credit Score Management
            'view.credit-scores',
            'create.credit-scores',
            'edit.credit-scores',
            'delete.credit-scores',

            // Degree Management
            'view.degrees',
            'create.degrees',
            'edit.degrees',
            'delete.degrees',

            // Enrollment Management
            'view.enrollments',
            'create.enrollments',
            'edit.enrollments',
            'delete.enrollments',

            // Faculty Management
            'view.faculties',
            'create.faculties',
            'edit.faculties',
            'delete.faculties',

            // Gender Management
            'view.genders',
            'create.genders',
            'edit.genders',
            'delete.genders',

            // Instructor Management
            'view.instructors',
            'create.instructors',
            'edit.instructors',
            'delete.instructors',

            // Major Management
            'view.majors',
            'create.majors',
            'edit.majors',
            'delete.majors',

            // Financial Management
            'manage.fees',
            'view.payments',
            'create.payments',
            'edit.payments',
            'delete.payments',
            'manage.payments',
            'manage.scholarships',

            // Program Management
            'view.programs',
            'create.programs',
            'edit.programs',
            'delete.programs',
            'manage.programs',

            // Semester Management
            'view.semesters',
            'create.semesters',
            'edit.semesters',
            'delete.semesters',

            // Transaction Ledger Management
            'view.transaction-ledgers',
            'create.transaction-ledgers',
            'edit.transaction-ledgers',
            'delete.transaction-ledgers',

            // Permission Management
            'view.permissions',

            // Notification
            'send-notification',

            // Backup & Recovery
            'view.backups',
            'create.backups',
            'download.backups',
            'restore.backups',
            'delete.backups',
            'manage.backup',

            // Login History
            'view.login-histories',

            // Reports
            'view.reports',
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
            'view.students',
            'create.students',
            'edit.students',
            'manage_records.students',
            'view.programs',
            'create.programs',
            'edit.programs',
            'delete.programs',
            'view.courses',
            'create.courses',
            'edit.courses',
            'delete.courses',
            'manage.syllabus',
            'manage.classes',
            'view.reports',
            'view.semesters',
            'create.semesters',
            'edit.semesters',
            'delete.semesters',
            'view.enrollments',
            'create.enrollments',
            'edit.enrollments',
            'delete.enrollments',
            'view.academic-years',
            'create.academic-years',
            'edit.academic-years',
            'delete.academic-years',
            'view.academic-records',
            'create.academic-records',
            'edit.academic-records',
            'delete.academic-records',
            'view.class-schedules',
            'create.class-schedules',
            'edit.class-schedules',
            'delete.class-schedules',
            'view.degrees',
            'create.degrees',
            'edit.degrees',
            'delete.degrees',
            'view.majors',
            'create.majors',
            'edit.majors',
            'delete.majors',
            'view.genders',
            'create.genders',
            'edit.genders',
            'delete.genders',
            'view.contact-details',
            'create.contact-details',
            'edit.contact-details',
            'delete.contact-details',
        ]);

        $hod = SpatieRole::create([
            'name'           => 'hod',
            'description'    => 'Head of Department',
            'is_system_role' => true,
        ]);
        $hod->givePermissionTo([
            'view.departments',
            'create.departments',
            'edit.departments',
            'delete.departments',
            'manage.departments',
            'view.students',
            'manage_records.students',
            'view.programs',
            'create.programs',
            'edit.programs',
            'delete.programs',
            'view.courses',
            'create.courses',
            'edit.courses',
            'delete.courses',
            'manage.syllabus',
            'manage.classes',
            'view.academic-years',
            'view.academic-records',
            'view.class-schedules',
            'view.instructors',
            'view.semesters',
        ]);

        $professor = SpatieRole::create([
            'name'           => 'professor',
            'description'    => 'Teaching faculty',
            'is_system_role' => true,
        ]);
        $professor->givePermissionTo([
            'view.students',
            'view.courses',
            'create.courses',
            'edit.courses',
            'delete.courses',
            'manage.syllabus',
            'manage.classes',
            'view.academic-records',
            'create.academic-records',
            'edit.academic-records',
            'delete.academic-records',
            'view.attendances',
            'create.attendances',
            'edit.attendances',
            'delete.attendances',
            'view.class-schedules',
            'create.class-schedules',
            'edit.class-schedules',
            'delete.class-schedules',
            'view.enrollments',
            'create.enrollments',
            'edit.enrollments',
            'delete.enrollments',
            // Assuming a 'grades.manage' permission for professors, if applicable
            // 'grades.manage',
        ]);

        $staff = SpatieRole::create([
            'name'           => 'staff',
            'description'    => 'Administrative staff',
            'is_system_role' => true,
        ]);
        $staff->givePermissionTo([
            'view.students',
            'view.payments',
            'create.payments',
            'edit.payments',
            'delete.payments',
            'view.academic-records',
            'view.attendances',
            'view.classrooms',
            'view.class-schedules',
            'view.contact-details',
            'view.courses',
            'view.degrees',
            'view.enrollments',
            'view.faculties',
            'view.genders',
            'view.instructors',
            'view.majors',
            'view.programs',
            'view.semesters',
            'view.transaction-ledgers',
            'view.audit-logs',
            'view.login-histories',
            'view.reports',
            'view.system-configs',
            'view.academic-years',
        ]);

        $student = SpatieRole::create([
            'name'           => 'student',
            'description'    => 'Student access',
            'is_system_role' => true,
        ]);
        $student->givePermissionTo([
            'view.academic-records',
            'view.attendances',
            'view.courses',
            'view.enrollments',
            'view.payments',
            'view.semesters',
            'view.academic-years',
            'view.class-schedules',
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
        if (str_ends_with($permission, '.users')) {
            return 'User Management';
        }
        if (str_ends_with($permission, '.roles')) {
            return 'Role Management';
        }
        if (str_ends_with($permission, '.departments')) {
            return 'Department Management';
        }
        if (str_ends_with($permission, '.students')) {
            return 'Student Management';
        }
        if (str_ends_with($permission, '.system-configs')) {
            return 'System Configuration';
        }
        if (str_ends_with($permission, '.academic-years')) {
            return 'Academic Year Management';
        }
        if (str_ends_with($permission, '.academic-records')) {
            return 'Academic Record Management';
        }
        if (str_ends_with($permission, '.attendances')) {
            return 'Attendance Management';
        }
        if (str_ends_with($permission, '.audit-logs')) {
            return 'Audit Log Management';
        }
        if (str_ends_with($permission, '.classrooms')) {
            return 'Classroom Management';
        }
        if (str_ends_with($permission, '.class-schedules')) {
            return 'Class Schedule Management';
        }
        if (str_ends_with($permission, '.contact-details')) {
            return 'Contact Detail Management';
        }
        if (str_ends_with($permission, '.courses')) {
            return 'Course Management';
        }
        if (str_ends_with($permission, '.course-prerequisites')) {
            return 'Course Prerequisite Management';
        }
        if (str_ends_with($permission, '.credit-scores')) {
            return 'Credit Score Management';
        }
        if (str_ends_with($permission, '.degrees')) {
            return 'Degree Management';
        }
        if (str_ends_with($permission, '.enrollments')) {
            return 'Enrollment Management';
        }
        if (str_ends_with($permission, '.faculties')) {
            return 'Faculty Management';
        }
        if (str_ends_with($permission, '.genders')) {
            return 'Gender Management';
        }
        if (str_ends_with($permission, '.instructors')) {
            return 'Instructor Management';
        }
        if (str_ends_with($permission, '.majors')) {
            return 'Major Management';
        }
        if (str_ends_with($permission, '.payments')) {
            return 'Payment Management';
        }
        if (str_ends_with($permission, '.programs')) {
            return 'Program Management';
        }
        if (str_ends_with($permission, '.semesters')) {
            return 'Semester Management';
        }
        if (str_ends_with($permission, '.transaction-ledgers')) {
            return 'Transaction Ledger Management';
        }
        if (str_ends_with($permission, '.permissions')) {
            return 'Permission Management';
        }
        if (str_ends_with($permission, 'send-notification')) {
            return 'Notification';
        }
        if (str_ends_with($permission, '.backups')) {
            return 'Backup & Recovery';
        }
        if (str_ends_with($permission, '.login-histories')) {
            return 'Login History';
        }
        if (str_ends_with($permission, '.reports')) {
            return 'Reports';
        }
        if (str_ends_with($permission, '.audit')) {
            return 'Audit Management';
        }
        if (str_ends_with($permission, '.system')) {
            return 'System Management';
        }
        if (str_ends_with($permission, '.fees') || str_ends_with($permission, '.scholarships')) {
            return 'Financial Management';
        }
        if (str_ends_with($permission, '.syllabus') || str_ends_with($permission, '.classes')) {
            return 'Academic Management';
        }

        return 'General';
    }

    private function getPermissionDescription($permission): string
    {
        $descriptions = [
            // User Management
            'view.users' => 'View user accounts',
            'create.users' => 'Create new user accounts',
            'edit.users' => 'Edit existing user accounts',
            'delete.users' => 'Delete user accounts',
            'impersonate.users' => 'Impersonate other users',
            'edit-access.users' => 'Edit user access permissions',
            'update-access.users' => 'Update user access permissions',
            'status.users' => 'Update user account status',
            'restore.users' => 'Restore soft-deleted user accounts',
            'force-delete.users' => 'Permanently delete user accounts',

            // Role Management
            'view.roles' => 'View roles',
            'create.roles' => 'Create new roles',
            'edit.roles' => 'Edit existing roles',
            'delete.roles' => 'Delete roles',
            'assign.roles' => 'Assign roles to users',
            'edit-permissions.roles' => 'Edit permissions assigned to roles',
            'update-permissions.roles' => 'Update permissions assigned to roles',

            // Department Management
            'view.departments' => 'View departments',
            'create.departments' => 'Create new departments',
            'edit.departments' => 'Edit departments',
            'delete.departments' => 'Delete departments',
            'restore.departments' => 'Restore soft-deleted departments',
            'force-delete.departments' => 'Permanently delete departments',
            'manage.departments' => 'Manage department settings and data',

            // Student Management
            'view.students' => 'View student records',
            'create.students' => 'Create new student records',
            'edit.students' => 'Edit student records',
            'delete.students' => 'Delete student records',
            'restore.students' => 'Restore soft-deleted student records',
            'force-delete.students' => 'Permanently delete student records',
            'status.students' => 'Update student account status',
            'manage_records.students' => 'Manage student academic records',

            // System Configuration
            'view.system-configs' => 'View system configurations',
            'create.system-configs' => 'Create new system configurations',
            'edit.system-configs' => 'Edit existing system configurations',
            'delete.system-configs' => 'Delete system configurations',
            'system.config' => 'Access and modify system configurations (broad)',

            // Academic Year Management
            'view.academic-years' => 'View academic years',
            'create.academic-years' => 'Create new academic years',
            'edit.academic-years' => 'Edit existing academic years',
            'delete.academic-years' => 'Delete academic years',
            'manage.academic-years' => 'Manage academic year settings and data',

            // Academic Record Management
            'view.academic-records' => 'View academic records',
            'create.academic-records' => 'Create new academic records',
            'edit.academic-records' => 'Edit existing academic records',
            'delete.academic-records' => 'Delete academic records',

            // Attendance Management
            'view.attendances' => 'View attendance records',
            'create.attendances' => 'Create new attendance records',
            'edit.attendances' => 'Edit existing attendance records',
            'delete.attendances' => 'Delete attendance records',

            // Audit Log Management
            'view.audit-logs' => 'View audit logs',
            'create.audit-logs' => 'Create new audit logs', // Though typically auto-generated
            'edit.audit-logs' => 'Edit audit logs', // Though typically not editable
            'delete.audit-logs' => 'Delete audit logs',

            // Classroom Management
            'view.classrooms' => 'View classrooms',
            'create.classrooms' => 'Create new classrooms',
            'edit.classrooms' => 'Edit existing classrooms',
            'delete.classrooms' => 'Delete classrooms',

            // Class Schedule Management
            'view.class-schedules' => 'View class schedules',
            'create.class-schedules' => 'Create new class schedules',
            'edit.class-schedules' => 'Edit existing class schedules',
            'delete.class-schedules' => 'Delete class schedules',

            // Contact Detail Management
            'view.contact-details' => 'View contact details',
            'create.contact-details' => 'Create new contact details',
            'edit.contact-details' => 'Edit existing contact details',
            'delete.contact-details' => 'Delete contact details',

            // Course Management
            'view.courses' => 'View courses',
            'create.courses' => 'Create new courses',
            'edit.courses' => 'Edit existing courses',
            'delete.courses' => 'Delete courses',
            'manage.courses' => 'Manage courses (broad)',

            // Course Prerequisite Management
            'view.course-prerequisites' => 'View course prerequisites',
            'create.course-prerequisites' => 'Create new course prerequisites',
            'edit.course-prerequisites' => 'Edit existing course prerequisites',
            'delete.course-prerequisites' => 'Delete course prerequisites',

            // Credit Score Management
            'view.credit-scores' => 'View credit scores',
            'create.credit-scores' => 'Create new credit scores',
            'edit.credit-scores' => 'Edit existing credit scores',
            'delete.credit-scores' => 'Delete credit scores',

            // Degree Management
            'view.degrees' => 'View degrees',
            'create.degrees' => 'Create new degrees',
            'edit.degrees' => 'Edit existing degrees',
            'delete.degrees' => 'Delete degrees',

            // Enrollment Management
            'view.enrollments' => 'View enrollments',
            'create.enrollments' => 'Create new enrollments',
            'edit.enrollments' => 'Edit existing enrollments',
            'delete.enrollments' => 'Delete enrollments',

            // Faculty Management
            'view.faculties' => 'View faculties',
            'create.faculties' => 'Create new faculties',
            'edit.faculties' => 'Edit existing faculties',
            'delete.faculties' => 'Delete faculties',

            // Gender Management
            'view.genders' => 'View genders',
            'create.genders' => 'Create new genders',
            'edit.genders' => 'Edit existing genders',
            'delete.genders' => 'Delete genders',

            // Instructor Management
            'view.instructors' => 'View instructors',
            'create.instructors' => 'Create new instructors',
            'edit.instructors' => 'Edit existing instructors',
            'delete.instructors' => 'Delete instructors',

            // Major Management
            'view.majors' => 'View majors',
            'create.majors' => 'Create new majors',
            'edit.majors' => 'Edit existing majors',
            'delete.majors' => 'Delete majors',

            // Financial Management
            'manage.fees' => 'Manage student fees',
            'view.payments' => 'View payment records',
            'create.payments' => 'Create new payment records',
            'edit.payments' => 'Edit existing payment records',
            'delete.payments' => 'Delete payment records',
            'manage.payments' => 'Manage student payments (broad)',
            'manage.scholarships' => 'Manage scholarships',

            // Program Management
            'view.programs' => 'View academic programs',
            'create.programs' => 'Create new academic programs',
            'edit.programs' => 'Edit existing academic programs',
            'delete.programs' => 'Delete academic programs',
            'manage.programs' => 'Manage academic programs (broad)',

            // Semester Management
            'view.semesters' => 'View semesters',
            'create.semesters' => 'Create new semesters',
            'edit.semesters' => 'Edit existing semesters',
            'delete.semesters' => 'Delete semesters',

            // Transaction Ledger Management
            'view.transaction-ledgers' => 'View transaction ledger entries',
            'create.transaction-ledgers' => 'Create new transaction ledger entries',
            'edit.transaction-ledgers' => 'Edit existing transaction ledger entries',
            'delete.transaction-ledgers' => 'Delete transaction ledger entries',

            // Permission Management
            'permissions.view' => 'View permissions',

            // Notification
            'send-notification' => 'Send general notifications',

            // Backup & Recovery
            'view.backups' => 'View backup records',
            'create.backups' => 'Create new system backups',
            'download.backups' => 'Download system backups',
            'restore.backups' => 'Restore system from backup',
            'delete.backups' => 'Delete system backups',
            'manage.backup' => 'Manage system backups (broad)',

            // Login History
            'view.login-histories' => 'View login history records',

            // Reports
            'view.reports' => 'View system reports',
        ];

        return $descriptions[$permission] ?? 'No description available';
    }
}
