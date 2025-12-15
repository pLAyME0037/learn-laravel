<?php
namespace App\Livewire\Admin\Finance;

use App\Models\Invoice;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;

    public $statusFilter = ''; // 'unpaid', 'paid'
    public $search       = '';

    #[Layout('layouts.app', ['header' => 'Invoice Management'])]
    public function render()
    {
        $invoices = Invoice::with('student.user')
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn($q) => $q->whereHas('student', fn($s) => $s->where('student_id', 'like', "%$this->search%")))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.admin.finance.invoice-list', [
            'invoices' => $invoices,
            'totalDue' => Invoice::where('status', 'unpaid')->sum('amount') - Invoice::where('status', 'unpaid')->sum('paid_amount'),
        ]);
    }
}
