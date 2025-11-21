<?php

namespace Database\Seeders;

use App\Models\TransactionLedger;
use App\Models\User;
use App\Models\Payment; // Import Payment model
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionLedgerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get IDs for users and payments
        $userIds = User::pluck('id')->toArray();
        $paymentIds = Payment::pluck('id')->toArray();

        // Ensure necessary related data exists
        if (empty($userIds) || empty($paymentIds)) {
            $this->command->warn('' 
            . 'Some prerequisite data (Users or Payments) not found.'
            . ' Please seed them first.'
        );
            return;
        }

        // Define transaction types
        $transactionTypes = ['debit', 'credit'];

        // Generate sample transaction ledger entries
        // We'll link transactions to payments and users
        foreach ($paymentIds as $paymentId) {
            $payment = Payment::find($paymentId);
            if (!$payment) {
                $this->command->warn('' 
                . "Payment with ID {$paymentId} not found.'
                . ' Skipping transaction ledger entry."
            );
                continue;
            }

            // For each payment, create a corresponding ledger entry (e.g., a credit for the payment)
            // We'll also create some sample debits for users not directly tied to payments for demonstration.

            // Credit entry for the payment
            // Get the user_id associated with the student who made the payment
            $student = $payment->student;
            if (! $student || ! $student->user_id) {
                $this->command->warn('' 
                . "Student or User not found for payment ID {$paymentId}.'
                . ' Skipping credit transaction."
            );
                continue;
            }

            TransactionLedger::create([
                'user_id' => $student->user_id,
                'transaction_type' => 'credit',
                'credit' => $payment->amount,
                'debit' => 0.00,
                'created_at' => $payment->payment_date,
            ]);

            // Add some sample debit entries for users (e.g., for fees, expenses)
            // We'll pick a few random users for these debits
            $sampleUserIds = array_slice($userIds, 0, 3); // Take first 3 users as examples for debits
            foreach ($sampleUserIds as $userId) {
                // Avoid creating debits for the same user who just made a payment, if possible
                if ($userId === $payment->student_id) {
                    continue;
                }

                TransactionLedger::create([
                    'user_id' => $userId,
                    'transaction_type' => 'debit',
                    'credit' => 0.00,
                    'debit' => fake()->randomFloat(2, 50, 1000),
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
