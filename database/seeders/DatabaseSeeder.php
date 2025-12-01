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
            AcademicStructureSeeder::class,
            // DegreeSeeder::class,
            // FacultySeeder::class,
            // DepartmentSeeder::class,
            AdminSeeder::class,
            SystemConfigSeeder::class,
            InstructorSeeder::class,
            ProgramSeeder::class,
            StudentManagementSeeder::class,
            SemesterSeeder::class, // Creates AcademicYear and Semesters
            // AcademicYearDashboardSeeder::class, // Redundant, AcademicStructureSeeder handles AcademicYear and Semesters
            // MajorSeeder::class,
            ClassroomSeeder::class,
            // CourseSeeder::class, // Moved specific course seeding to AcademicStructureSeeder
            CoursePrerequisiteSeeder::class,
            ClassScheduleSeeder::class,
            EnrollmentSeeder::class, // Added to create enrollments
            AttendanceSeeder::class,
            ContactDetailSeeder::class,
            PaymentSeeder::class,
            TransactionLedgerSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
