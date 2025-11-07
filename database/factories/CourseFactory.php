<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'program_id' => Program::factory(),
            'name' => 'Introduction to ' . $this->faker->word(),
            'code' => $this->faker->unique()->bothify('COURSE-???-###'),
            'description' => $this->faker->sentence(),
            'credits' => $this->faker->numberBetween(1, 5),
            'max_students' => $this->faker->numberBetween(20, 100),
            'start_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+2 months', '+3 months'),
            'status' => 'active',
        ];
    }
}
