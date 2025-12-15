<?php

namespace App\Livewire\Admin\Finance;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class InvoiceList extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $statusFilter = '';

    // Modal State
    public $showPaymentModal = false;
    public $selectedInvoice = null;
    
    // Payment Form Data
    public $payAmount;
    public $payReference; // e.g. "Cash", "Receipt #123"

    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }

    // --- Actions ---

    public function openPaymentModal($id)
    {
        $this->selectedInvoice = Invoice::with('student.user')->find($id);
        
        if (!$this->selectedInvoice) return;

        // Default to paying the remaining balance
        $this->payAmount = $this->selectedInvoice->amount - $this->selectedInvoice->paid_amount;
        $this->payReference = '';
        $this->showPaymentModal = true;
    }

    public function savePayment()
    {
        if (!$this->selectedInvoice) return;

        $balance = $this->selectedInvoice->amount - $this->selectedInvoice->paid_amount;

        $this->validate([
            'payAmount' => 'required|numeric|min:0.01|max:' . $balance,
            'payReference' => 'nullable|string|max:50',
        ]);

        // 1. Update Invoice
        $this->selectedInvoice->paid_amount += $this->payAmount;

        // 2. Check Status
        if ($this->selectedInvoice->paid_amount >= $this->selectedInvoice->amount) {
            $this->selectedInvoice->status = 'paid';
            // Clear Financial Hold on Student
            $this->selectedInvoice->student->update(['has_outstanding_balance' => false]);
        } else {
            $this->selectedInvoice->status = 'partial';
        }

        $this->selectedInvoice->save();

        // 3. Log Logic (Optional: You could create a Transaction model here)
        // Transaction::create([...]);

        $this->showPaymentModal = false;
        $this->dispatch('swal:success', ['message' => 'Payment recorded successfully.']);
    }

    #[Layout('layouts.app', ['header' => 'Invoice Management'])]
    public function render()
    {
        $invoices = Invoice::with(['student.user', 'student.program'])
            ->when($this->search, function($q) {
                $q->whereHas('student', function($s) {
                    $s->where('student_id', 'like', '%'.$this->search.'%')
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'));
                })->orWhere('id', $this->search);
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.finance.invoice-list', [
            'invoices' => $invoices,
            'totalOutstanding' => Invoice::where('status', '!=', 'paid')->sum('amount') - Invoice::where('status', '!=', 'paid')->sum('paid_amount')
        ]);
    }
}