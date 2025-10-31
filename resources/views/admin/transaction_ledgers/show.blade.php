<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Ledger Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <x-input-label for="user_name"
                            :value="__('User Name')" />
                        <p>{{ $transactionLedger->user->name }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="transaction_type"
                            :value="__('Transaction Type')" />
                        <p>{{ $transactionLedger->transaction_type }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="amount"
                            :value="__('Amount')" />
                        <p>{{ $transactionLedger->amount }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="currency"
                            :value="__('Currency')" />
                        <p>{{ $transactionLedger->currency }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description"
                            :value="__('Description')" />
                        <p>{{ $transactionLedger->description }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="transaction_date"
                            :value="__('Transaction Date')" />
                        <p>{{ 
                            $transactionLedger->transaction_date 
                            ? \Carbon\Carbon::parse($transactionLedger->transaction_date)->format('d-m-Y H:i:s') 
                            : '' 
                        }}</p>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.transaction-ledgers.edit', $transactionLedger) }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.transaction-ledgers.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
