<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\TransactionLedger;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionLedgerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fetch Payments with linked User ID efficiently
        // Only get payments where the student actually has a user account
        $payments = Payment::with('student:id,user_id')
            ->whereHas('student')
            ->get();

        if ($payments->isEmpty()) {
            $this->command->warn('No payments found. Run PaymentSeeder first.');
            return;
        }

        $ledgersToInsert = [];
        $now = now();

        // 2. Generate Credit Entries (From Payments)
        foreach ($payments as $payment) {
            $userId = $payment->student->user_id ?? null;
            if (!$userId) continue;

            $ledgersToInsert[] = [
                'user_id' => $userId,
                'transaction_type' => 'credit',
                'amount' => $payment->amount, // Assuming amount is stored positively
                'reference_id' => $payment->id, // Link to payment (optional but good)
                'description' => 'Tuition Payment',
                'created_at' => $payment->payment_date ?? $now,
                'updated_at' => $now,
            ];
        }

        // 3. Generate Random Debit Entries (Fees)
        // Pick 50 random users to apply fees to
        $randomUserIds = User::inRandomOrder()->take(50)->pluck('id');

        foreach ($randomUserIds as $userId) {
            $ledgersToInsert[] = [
                'user_id' => $userId,
                'transaction_type' => 'debit',
                'amount' => -rand(50, 500), // Negative for debit? Or positive with type 'debit'?
                // Adjust based on your Ledger logic. Standard is usually:
                // Credit = +Amount, Debit = -Amount (or separate columns 'credit'/'debit')
                'reference_id' => null,
                'description' => 'Library Fee / Lab Fee',
                'created_at' => $now->subDays(rand(1, 30)),
                'updated_at' => $now,
            ];
        }

        // 4. Bulk Insert
        // Adjust columns to match your schema (credit/debit columns vs single amount column)
        // Based on your previous code, you used specific 'credit' and 'debit' columns.
        
        $finalData = array_map(function ($item) {
            $isCredit = $item['transaction_type'] === 'credit';
            return [
                'user_id' => $item['user_id'],
                'transaction_type' => $item['transaction_type'],
                'credit' => $isCredit ? abs($item['amount']) : 0,
                'debit' => !$isCredit ? abs($item['amount']) : 0,
                'created_at' => $item['created_at'],
                'updated_at' => $item['updated_at'],
            ];
        }, $ledgersToInsert);

        foreach (array_chunk($finalData, 500) as $chunk) {
            TransactionLedger::insert($chunk);
        }
    }
}