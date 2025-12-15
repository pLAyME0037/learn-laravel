<?php

namespace App\Livewire\Academic;

use App\Models\Invoice;
use App\Models\Student;
use Livewire\Component;
use Livewire\Attributes\Layout;

class StudentFinancials extends Component
{
    public $invoices = [];
    public $totalDue = 0;

    public function mount()
    {
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return; // Or handle error
        }

        // Get Invoices ordered by newest
        $this->invoices = Invoice::where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->get();

        // Calculate Total Unpaid
        $this->totalDue = $this->invoices->where('status', '!=', 'paid')->sum(function($inv) {
            return $inv->amount - $inv->paid_amount;
        });
    }

    // Mock Payment Function (For Demo)
    public function payMock($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        
        if ($invoice) {
            $invoice->update([
                'paid_amount' => $invoice->amount,
                'status' => 'paid'
            ]);
            
            // Remove hold if all cleared
            $invoice->student->update(['has_outstanding_balance' => false]);
            
            session()->flash('success', 'Payment successful (Mock)!');
            $this->mount(); // Refresh data
        }
    }

    #[Layout('layouts.app', ['header' => 'My Financials'])]
    public function render()
    {
        return view('livewire.academic.student-financials');
    }
}