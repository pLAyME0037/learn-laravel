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
        // Create specific types of fake users
        // User::factory(10)->create();
        // User::factory()->count(5)->withoutProfilePic()->create();
        // User::factory()->count(3)->withLongBio()->unverified()->create();

        $this->call([
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            AdminSeeder::class,
            ProgramSeeder::class,
            StudentManagementSeeder::class,
        ]);
    }
}
