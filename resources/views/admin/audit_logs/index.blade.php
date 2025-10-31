<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Manage Audit Logs</h3>
                    </div>

                    <div class="mb-4">
                        <input type="text"
                            placeholder="Search audit logs..."
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>

                    @php
                        $headers = [
                            'user' => 'Username',
                            'action' => 'Action',
                            'description' => 'Description',
                        ];

                        $data = $auditLogs->map(function ($auditLog) {
                            return [
                                'user' => $auditLog->user->name,
                                'action' => $auditLog->action,
                                'description' => $auditLog->description,
                            ];
                        });

                        $actions = [
                            'delete' => [
                                'route' => 'admin.auditLogs.destroy',
                                'label' => 'Delete',
                                'params' => ['audit_log' => 'id'],
                                'class' => 'text-red-600 hover:text-red-900',
                                'method' => 'DELETE',
                            ],
                        ];
                    @endphp
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <x-dynamic-table :headers="$headers"
                        :data="$data"
                        :actions="$actions"
                        :options="['wrapperClass' => 'border border-gray-200']" />

                    <div class="mt-4">
                        {{ $auditLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
