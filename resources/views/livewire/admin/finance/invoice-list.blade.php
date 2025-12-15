<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Summary Card -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6 border-l-4 border-red-500">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">Total Outstanding Due</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($totalDue, 2) }}</p>
        </div>

        <!-- Filters -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-4">
                <input wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search Student ID..."
                    class="rounded-md border-gray-300 dark:bg-gray-900">
                <select wire:model.live="statusFilter"
                    class="rounded-md border-gray-300 dark:bg-gray-900">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($invoices as $inv)
                        <tr>
                            <td class="px-6 py-4 text-xs font-mono text-gray-500">#{{ $inv->id }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $inv->student->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $inv->student->student_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $inv->semester_name }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                                ${{ number_format($inv->amount, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-red-600">
                                ${{ number_format($inv->balance, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2 py-1 text-xs font-bold rounded-full 
                                    {{ $inv->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($inv->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="#"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    View
                                 </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">{{ $invoices->links() }}</div>
        </div>
    </div>
</div>
