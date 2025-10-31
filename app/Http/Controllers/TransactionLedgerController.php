<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TransactionLedger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionLedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $transactionLedgers = TransactionLedger::with('user')->paginate(10);
        return view('admin.transaction_ledgers.index', compact('transactionLedgers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::all();
        return view('admin.transaction_ledgers.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'transaction_type' => 'required|string|max:255', // e.g., Credit, Debit
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255|unique:transaction_ledgers,reference_number',
        ]);

        TransactionLedger::create($validated);

        return redirect()->route('transaction-ledgers.index')->with('success', 'Transaction ledger entry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionLedger $transactionLedger): View
    {
        $transactionLedger->load('user');
        return view('admin.transaction_ledgers.show', compact('transactionLedger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionLedger $transactionLedger): View
    {
        $users = User::all();
        return view('admin.transaction_ledgers.edit', compact('transactionLedger', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionLedger $transactionLedger): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'transaction_type' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:255|unique:transaction_ledgers,reference_number,' . $transactionLedger->id,
        ]);

        $transactionLedger->update($validated);

        return redirect()->route('transaction-ledgers.show', $transactionLedger)->with('success', 'Transaction ledger entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionLedger $transactionLedger): RedirectResponse
    {
        $transactionLedger->delete();

        return redirect()->route('transaction-ledgers.index')->with('success', 'Transaction ledger entry deleted successfully.');
    }
}
