<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            FacultySeeder::class,
            DepartmentSeeder::class,
            AdminSeeder::class,
            SystemConfigSeeder::class,
            InstructorSeeder::class,
            ProgramSeeder::class,
            StudentManagementSeeder::class,
            SemesterSeeder::class, // Creates AcademicYear and Semesters
            AcademicYearDashboardSeeder::class, // Now fetches existing AcademicYear and Semesters
            ClassroomSeeder::class,
            CourseSeeder::class,
            CoursePrerequisiteSeeder::class,
            ClassScheduleSeeder::class,
            AttendanceSeeder::class,
            ContactDetailSeeder::class,
            PaymentSeeder::class,
            TransactionLedgerSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
