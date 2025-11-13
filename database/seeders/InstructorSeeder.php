<?php

namespace Database\Seeders;

use App\Models\Instructor;
use App\Models\Faculty; // Import Faculty model
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory; // Import Faker Factory

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialize Faker
        $faker = Factory::create();

        // Get existing faculty IDs to assign instructors to them
        $facultyIds = Faculty::pluck('id')->toArray();

        // Ensure there are faculties to assign instructors to
        if (empty($facultyIds)) {
            $this->command->warn('No faculties found. Please seed faculties first.');
            return;
        }

        // Create a few instructors using the factory
        // The factory will handle assigning users and departments
        Instructor::factory()->count(10)->create([
            'faculty_id' => function () use ($facultyIds, $faker) { // Pass $faker to the closure
                return $faker->randomElement($facultyIds);
            },
        ]);
    }
}
