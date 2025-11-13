<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Instructor;
use App\Models\User;
use App\Models\Faculty; // Import Faculty model
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instructor>
 */
class InstructorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get existing department IDs and user IDs to ensure valid foreign keys
        $departmentIds = Department::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $facultyIds = Faculty::pluck('id')->toArray(); // Get faculty IDs

        // If no departments or users or faculties exist, return empty or handle appropriately
        // For seeding, we assume these exist or will be seeded before instructors.
        // If they don't exist, this will throw an error, which is expected if dependencies aren't met.
        if (empty($departmentIds) || empty($userIds) || empty($facultyIds)) {
            // This scenario should ideally not happen if other seeders run first.
            // For robustness, one might add a check in the seeder itself.
            // For now, we'll assume they exist.
            // If this factory is used standalone, it might need to create users/departments/faculties first.
        }

        return [
            'payscale' => $this->faker->numberBetween(30000, 100000),
            'department_id' => $this->faker->randomElement($departmentIds),
            'user_id' => $this->faker->randomElement($userIds),
            'faculty_id' => $this->faker->randomElement($facultyIds), // Correctly use faculty IDs for faculty_id
        ];
    }
}
