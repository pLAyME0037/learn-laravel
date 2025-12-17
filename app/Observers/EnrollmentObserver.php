<?php

namespace App\Observers;

use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\SystemConfig;

class EnrollmentObserver
{
    /**
     * Handle the Enrollment "created" event.
     */
    public function created(Enrollment $enrollment): void
    {
        // 1. Get Context
        $classSession = $enrollment->classSession;
        $course = $classSession->course;
        $semester = $classSession->semester;
        $student = $enrollment->student;

        // Hybrid: Charge per credit based on Major's setting or System Default
        // $costPerCredit = 50.00; // Default
        // OR fetch from SystemConfig
        $costPerCredit = SystemConfig::get('cost_per_credit', 50);
        
        // Calculate
        $amountToAdd = $course->credits * $costPerCredit;

        // 3. Find or Create Invoice for THIS Semester
        $invoice = Invoice::firstOrCreate(
            [
                'student_id' => $student->id,
                'semester_name' => $semester->name
            ],
            [
                'amount' => 0,
                'paid_amount' => 0,
                'status' => 'unpaid'
            ]
        );

        // 4. Increment Amount
        $invoice->increment('amount', $amountToAdd);
        
        // 5. Update Student "Hold" Status
        if ($invoice->amount > $invoice->paid_amount) {
            $student->update(['has_outstanding_balance' => true]);
        }
    }

    /**
     * Handle the Enrollment "deleted" event (Dropped Class).
     */
    public function deleted(Enrollment $enrollment): void
    {
        // Reverse the charge
        $classSession = $enrollment->classSession;
        $course = $classSession->course;
        $semester = $classSession->semester;
        
        $costPerCredit = 50.00;
        $amountToRemove = $course->credits * $costPerCredit;

        $invoice = Invoice::where('student_id', $enrollment->student_id)
            ->where('semester_name', $semester->name)
            ->first();

        if ($invoice) {
            // Decrement but don't go below zero
            $newAmount = max(0, $invoice->amount - $amountToRemove);
            $invoice->update(['amount' => $newAmount]);

            // Check if they are now fully paid (e.g., dropped class leads to $0 balance)
            if ($invoice->paid_amount >= $newAmount) {
                $invoice->update(['status' => 'paid']);
                $enrollment->student->update(['has_outstanding_balance' => false]);
            }
        }
    }
}