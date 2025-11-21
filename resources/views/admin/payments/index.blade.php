<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payments') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Manage Payments
                    </h3>
                    <a href="{{ route('admin.payments.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Add New Payment') }}
                    </a>
                </div>

                <div class="mb-4">
                    <input type="text"
                        placeholder="Search payments..."
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                </div>

                @php
                    $headers = [
                        'student_name' => 'Student Name',
                        'amount' => 'Amount',
                        'payment_date' => 'Payment Date',
                        'payment_method' => 'Method',
                        'status' => 'Status',
                    ];

                    $data = $payments->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'student_name' => $payment->student?->user->name,
                            'amount' => $payment->formatted_amount,
                            'payment_date' => $payment->formatted_payment_date,
                            'payment_method' => $payment->payment_method,
                            'status' => $payment->status,
                        ];
                    });

                    $actions = [
                        'show' => [
                            'route' => 'admin.payments.show',
                            'label' => 'View',
                            'params' => ['payment' => 'id'],
                            'class' => 'text-blue-600 hover:text-blue-900',
                        ],
                        'edit' => [
                            'route' => 'admin.payments.edit',
                            'label' => 'Edit',
                            'params' => ['payment' => 'id'],
                            'class' => 'text-indigo-600 hover:text-indigo-900',
                        ],
                        'delete' => [
                            'route' => 'admin.payments.destroy',
                            'label' => 'Delete',
                            'params' => ['payment' => 'id'],
                            'class' => 'text-red-600 hover:text-red-900',
                            'method' => 'DELETE',
                        ],
                    ];
                @endphp

                <x-dynamic-table :headers="$headers"
                    :data="$data"
                    :actions="$actions"
                    :options="['wrapperClass' => 'border border-gray-200']" />

                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
