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
            // CamGeoSeeder::class,
            // LocationSeeder::class,
            DictionarySeeder::class,
            // AcademicStructureSeeder::class,
            AdminSeeder::class,
            SystemConfigSeeder::class,
            InstructorSeeder::class,
            StudentManagementSeeder::class,
            ClassroomSeeder::class,
        ]);
    }
}
