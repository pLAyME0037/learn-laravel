<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean Slate
        Schema::disableForeignKeyConstraints();
        Payment::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Fetch IDs
        $studentIds = Student::pluck('id')->toArray();

        if (empty($studentIds)) {
            $this->command->warn('No students found. Run StudentSeeder first.');
            return;
        }

        $paymentsToInsert = [];
        $now = now();
        $periods = [
            'Tuition' => ['min' => 10000, 'max' => 20000],
            'Lab'     => ['min' => 50, 'max' => 500],
            'Library' => ['min' => 10, 'max' => 100],
        ];
        $statuses = ['completed', 'completed', 'completed', 'pending', 'failed'];

        // 3. Generate Data
        foreach ($studentIds as $studentId) {
            // Create 1-3 payments per student
            $count = rand(1, 3);

            for ($i = 0; $i < $count; $i++) {
                // Pick a random type (Tuition, Lab, etc.)
                $typeKey = array_rand($periods);
                $config = $periods[$typeKey];
                
                // Generate amount
                $amount = rand($config['min'] * 100, $config['max'] * 100) / 100;
                
                $paymentsToInsert[] = [
                    'student_id' => $studentId,
                    'amount' => $amount,
                    'payment_date' => $now->copy()->subDays(rand(1, 180)),
                    'payment_period_description' => "$typeKey Fees - Fall 2025",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // 4. Bulk Insert (Chunked for safety)
        foreach (array_chunk($paymentsToInsert, 1000) as $chunk) {
            Payment::insert($chunk);
        }
    }
}