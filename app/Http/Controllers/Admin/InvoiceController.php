<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('admin.finance.invoices.index');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('student.user');
        return view('admin.finance.invoices.show', compact('invoice'));
    }

    /**
     * Record a Payment.
     */
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount'    => 'required|numeric|min:0.01|max:' . $invoice->balance,
            'reference' => 'nullable|string',
        ]);

        $invoice->paid_amount += $validated['amount'];

        if ($invoice->paid_amount >= $invoice->amount) {
            $invoice->status = 'paid';
            // Update Student Financial Hold status
            $invoice->student->update(['has_outstanding_balance' => false]);
        } elseif ($invoice->paid_amount > 0) {
            $invoice->status = 'partial';
        }

        $invoice->save();

        return back()->with('success', 'Payment recorded.');
    }
}
