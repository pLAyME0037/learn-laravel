<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    @if (
        !auth()->user()
            ?->hasAnyRole(['admin', 'Super Administrator', 'staff']))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Icon -->
                    <svg class="h-5 w-5 text-yellow-400"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        You do not have permission to access this URL.
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                    Total Outstanding
                </h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    ${{ number_format($totalOutstanding, 2) }}
                </p>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex gap-4 w-full md:w-2/3">
                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search Student Name or ID..."
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">

                <select wire:model.live="statusFilter"
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                    <option value="">All Statuses</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="partial">Partial</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-xs font-mono text-gray-500">#{{ $inv->id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $inv->student->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $inv->student->student_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $inv->semester_name }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                                ${{ number_format($inv->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-green-600">
                                ${{ number_format($inv->paid_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-red-600">
                                ${{ number_format($inv->amount - $inv->paid_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2 py-1 text-xs font-bold rounded-full 
                                {{ $inv->status === 'paid' ? 'bg-green-100 text-green-800' : ($inv->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($inv->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                @if ($inv->status !== 'paid')
                                    <button wire:click="openPaymentModal({{ $inv->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Record Payment
                                    </button>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed">
                                        Completed
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8"
                                class="px-6 py-8 text-center text-gray-500">
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t dark:border-gray-700">
                {{ $invoices->links() }}
            </div>
        </div>

        <!-- PAYMENT MODAL -->
        @if ($showPaymentModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500 bg-opacity-75">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl transform transition-all">
                    <h3 class="text-lg font-bold mb-4 dark:text-white">
                        Record Payment
                        <span class="text-sm font-normal text-gray-500 block mt-1">
                            Invoice #{{ $selectedInvoice->id }} - {{ $selectedInvoice->student->user->name }}
                        </span>
                    </h3>

                    <div class="space-y-4">
                        <div
                            class="bg-gray-50 dark:bg-gray-700 p-3 rounded text-sm text-gray-700 dark:text-gray-300 flex justify-between">
                            <span>Balance Due:</span>
                            <span class="font-bold text-red-600">
                                ${{ number_format($selectedInvoice->amount - $selectedInvoice->paid_amount, 2) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Amount Received ($)
                            </label>
                            <input type="number"
                                step="0.01"
                                wire:model="payAmount"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('payAmount')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Reference / Method
                            </label>
                            <input type="text"
                                wire:model="payReference"
                                placeholder="e.g. Cash, Check #123"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('payReference')
                                <span class="text-red-500 text-xs">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button wire:click="$set('showPaymentModal', false)"
                            class="px-4 py-2 text-gray-500 hover:text-gray-700">
                            Cancel
                        </button>
                        <button wire:click="savePayment"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 shadow">
                            Confirm Payment
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>
@push('scripts')
    <x-sweet-alert />
@endpush
