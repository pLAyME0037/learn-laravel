<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Summary Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 {{ $totalDue > 0 ? 'border-red-500' : 'border-green-500' }}">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">
                  Outstanding Balance
               </h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    ${{ number_format($totalDue, 2) }}
                </p>
                <p class="text-xs mt-2 {{ $totalDue > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $totalDue > 0 ? 'Action Required' : 'All Clear' }}
                </p>
            </div>
        </div>

        <!-- Invoice List -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                  Invoice History
               </h3>
            </div>

            @if (count($invoices) > 0)
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Semester</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Paid</th>
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($invoices as $inv)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $inv->semester_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $inv->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($inv->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-500 dark:text-gray-400">
                                    ${{ number_format($inv->paid_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $inv->status === 'paid' ? 'bg-green-100 text-green-800' : ($inv->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    @if ($inv->status !== 'paid')
                                        <button wire:click="payMock({{ $inv->id }})"
                                            wire:confirm="Confirm payment of ${{ number_format($inv->amount - $inv->paid_amount, 2) }}?"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold">
                                            Pay Now
                                        </button>
                                    @else
                                        <span class="text-green-600 dark:text-green-400">
                                          Receipt
                                       </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6 text-center text-gray-500">
                  No invoices found.
               </div>
            @endif
        </div>
    </div>
</div>
