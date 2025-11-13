<?php
namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get student IDs (the integer primary key)
        $studentIds = Student::pluck('id')->toArray();

        // Ensure students exist
        if (empty($studentIds)) {
            $this->command->warn('No students found. Please seed students first.');
            return;
        }

        // Define payment statuses and periods
        $statuses = ['completed', 'pending', 'failed'];
        $periods  = ['Tuition Fee - Fall 2025', 'Lab Fees - Fall 2025', 'Library Fees - Fall 2025'];

        // Generate sample payments for students
        foreach ($studentIds as $studentId) {
            // Create a few payments per student
            for ($i = 0; $i < rand(1, 3); $i++) {
                $paymentDate = Carbon::now()->subMonths(rand(0, 6)); // Payment within the last 6 months
                $status      = $statuses[array_rand($statuses)];
                $period      = $periods[array_rand($periods)];

                // Ensure amount is reasonable for the period
                $amount = match ($period) {
                    'Tuition Fee - Fall 2025'  => fake()->randomFloat(2, 10000, 20000),
                    'Lab Fees - Fall 2025'     => fake()->randomFloat(2, 50, 500),
                    'Library Fees - Fall 2025' => fake()->randomFloat(2, 10, 100),
                    default                    => fake()->randomFloat(2, 10, 20000),
                };

                Payment::create([
                    'student_id'                 => $studentId,
                    'amount'                     => $amount,
                    'payment_date'               => $paymentDate,
                    'payment_period_description' => $period,
                ]);
            }
        }
    }
}
