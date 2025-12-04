<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Gender;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'                    => User::factory(),
            'department_id'              => Department::factory(),
            'program_id'                 => Program::factory(),
            'student_id'                 => $this->faker->unique()->bothify('STU######'),
            'registration_number'        => $this->faker->unique()->bothify('REG######'),
            'date_of_birth'              => $this->faker->date(),
            'gender_id'                  => Gender::inRandomOrder()->first()->id,
            'nationality'                => $this->faker->country,
            'id_card_number'             => $this->faker->unique()->numerify('###########'),
            'passport_number'            => $this->faker->unique()->bothify('PAS######'),
            'phone'                      => $this->faker->phoneNumber,
            'emergency_contact_name'     => $this->faker->name,
            'emergency_contact_phone'    => $this->faker->phoneNumber,
            'emergency_contact_relation' => $this->faker->randomElement(['Parent', 'Sibling', 'Guardian']),
            'current_address'            => $this->faker->address,
            'city'                       => $this->faker->city,
            'state'                      => $this->faker->state,
            'country'                    => $this->faker->country,
            'postal_code'                => $this->faker->postcode,
            'admission_date'             => $this->faker->date(),
            'expected_graduation'        => $this->faker->date(),
            'current_semester'           => $this->faker->numberBetween(1, 12),
            'cgpa'                       => $this->faker->randomFloat(2, 0, 4),
            'total_credits_earned'       => $this->faker->numberBetween(0, 150),
            'academic_status'            => $this->faker->randomElement(['active', 'probation', 'suspended', 'graduated', 'withdrawn', 'transfered']),
            'enrollment_status'          => $this->faker->randomElement(['full_time', 'part_time', 'exchange', 'study_abroad']),
            'fee_category'               => $this->faker->randomElement(['regular', 'scholarship', 'financial_aid', 'self_financed']),
            'has_outstanding_balance'    => $this->faker->boolean,
            'previous_education'         => $this->faker->sentence,
            'blood_group'                => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'has_disability'             => $this->faker->boolean,
            'disability_details'         => $this->faker->boolean ? $this->faker->sentence : null,
            'metadata'                   => [],
        ];
    }
}
