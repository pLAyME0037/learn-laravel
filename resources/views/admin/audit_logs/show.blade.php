<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Log Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Audit Log Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('User')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Action')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->action }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Table Name')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->table_name }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Record ID')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->record_id }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Old Values')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->old_values }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('New Values')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->new_values }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('URL')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->url }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('IP Address')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->ip_address }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('User Agent')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->user_agent }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Created At')" />
                            <p class="mt-1 text-sm text-gray-600  dark:text-gray-400">{{ $auditLog->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.audit-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
