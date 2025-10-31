<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Transaction Ledger') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.transaction-ledgers.store') }}">
                        @csrf

                        <!-- User ID -->
                        <div>
                            <x-input-label for="user_id" :value="__('User')" />
                            <x-select-input id="user_id" class="block mt-1 w-full" name="user_id" :value="old('user_id')" required autofocus>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        <!-- Transaction Type -->
                        <div class="mt-4">
                            <x-input-label for="transaction_type" :value="__('Transaction Type')" />
                            <x-text-input id="transaction_type" class="block mt-1 w-full" type="text" name="transaction_type" :value="old('transaction_type')" required />
                            <x-input-error :messages="$errors->get('transaction_type')" class="mt-2" />
                        </div>

                        <!-- Amount -->
                        <div class="mt-4">
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" name="amount" :value="old('amount')" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <!-- Currency -->
                        <div class="mt-4">
                            <x-input-label for="currency" :value="__('Currency')" />
                            <x-text-input id="currency" class="block mt-1 w-full" type="text" name="currency" :value="old('currency')" required />
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-textarea-input id="description" class="block mt-1 w-full" name="description">{{ old('description') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Transaction Date -->
                        <div class="mt-4">
                            <x-input-label for="transaction_date" :value="__('Transaction Date')" />
                            <x-text-input id="transaction_date" class="block mt-1 w-full" type="datetime-local" name="transaction_date" :value="old('transaction_date')" required />
                            <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Create') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
