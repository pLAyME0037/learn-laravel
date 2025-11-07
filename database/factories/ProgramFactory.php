<?php
namespace Database\Factories;

use App\Models\Degree;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id'          => Department::factory(),
            'degree_id'              => Degree::factory(),
            'name'                   => $this->faker->jobTitle(),
            'code'                   => $this->faker->unique()->bothify('PROG-####'),
            'description'            => $this->faker->paragraph(),
            'level'                  => $this->faker->randomElement(['undergraduate', 'graduate', 'postgraduate']),
            'duration_years'         => $this->faker->numberBetween(1, 5),
            'total_semesters'        => $this->faker->numberBetween(2, 10),
            'total_credits_required' => $this->faker->numberBetween(60, 180),
            'tuition_fee'            => $this->faker->randomFloat(2, 5000, 25000),
            'curriculum'             => ['core' => ['CS101', 'MATH203'], 'electives' => ['ART101', 'HIST301']],
            'is_active'              => true,
        ];
    }
}
