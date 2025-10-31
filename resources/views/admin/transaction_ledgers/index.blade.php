<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Ledgers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.transaction-ledgers.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Add New Transaction Ledger') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @php
                        $headers = [
                            'id' => 'ID',
                            'user_id' => 'User Name',
                            'transaction_type' => 'Transaction Type',
                            'amount' => 'Amount',
                            'currency' => 'Currency',
                            'description' => 'Description',
                            'transaction_date' => 'Transaction Date',
                            'created_at' => 'Created At',
                            'updated_at' => 'Updated At',
                        ];

                        $data = $transactionLedgers->map(function ($ledger) {
                            return [
                                'id' => $ledger->id,
                                'user_id' => $ledger->user->name,
                                'transaction_type' => $ledger->transaction_type,
                                'amount' => $ledger->amount,
                                'currency' => $ledger->currency,
                                'description' => $ledger->description,
                                'transaction_date' => $ledger->transaction_date ? \Carbon\Carbon::parse($ledger->transaction_date)->format('d-m-Y H:i:s') : '',
                                'created_at' => $ledger->created_at ? \Carbon\Carbon::parse($ledger->created_at)->format('d-m-Y H:i:s') : '',
                                'updated_at' => $ledger->updated_at ? \Carbon\Carbon::parse($ledger->updated_at)->format('d-m-Y H:i:s') : '',
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.transaction_ledgers.show',
                                'label' => 'View',
                                'params' => ['transaction_ledger'],
                            ],
                            'edit' => [
                                'route' => 'admin.transaction_ledgers.edit',
                                'label' => 'Edit',
                                'params' => ['transaction_ledger'],
                            ],
                            'delete' => [
                                'route' => 'admin.transaction_ledgers.destroy',
                                'label' => 'Delete',
                                'params' => ['transaction_ledger'],
                                'confirm' => 'Are you sure you want to delete this transaction ledger?',
                            ],
                        ];
                    @endphp

                    <x-dynamic-table :headers="$headers" :data="$data" :actions="$actions" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
