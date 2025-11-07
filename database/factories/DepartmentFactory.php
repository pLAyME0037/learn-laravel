<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company() . ' Department',
            'code' => $this->faker->unique()->bothify('DEPT-###'),
            'description' => $this->faker->paragraph(),
            'hod_id' => User::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'office_location' => 'Building ' . $this->faker->buildingNumber() . ', Room ' . $this->faker->randomNumber(3),
            'established_year' => $this->faker->year(),
            'budget' => $this->faker->randomFloat(2, 10000, 100000),
            'is_active' => true,
            'metadata' => ['info' => 'some extra info'],
        ];
    }
}
