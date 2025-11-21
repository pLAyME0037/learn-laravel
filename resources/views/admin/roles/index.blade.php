<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Roles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Manage Roles
                        </h3>
                        <a href="{{ route('admin.roles.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-800 dark:bg-blue-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-blue-800 uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Add New Role
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        @php
                            $headers = [
                                'id' => 'ID',
                                'name' => 'Current Roles',
                                'created_at' => 'Created At',
                            ];

                            $data = $roles->map(function ($role) {
                                return [
                                    'id' => $role->id,
                                    'name' => $role->name,
                                    'created_at' => $role->created_at->format('d M Y H:i'),
                                ];
                            });

                            $actions = [
                                'view' => [
                                    'route' => 'admin.roles.show',
                                    'label' => 'View',
                                    'params' => ['role' => 'id'],
                                    'class' => 'text-indigo-600 hover:text-indigo-900 mr-3',
                                ],
                                'edit' => [
                                    'route' => 'admin.roles.edit',
                                    'label' => 'Edit',
                                    'params' => ['role' => 'id'],
                                    'class' => 'text-green-600 hover:text-green-900 mr-3',
                                ],
                                'edit-permission' => [
                                    'route' => 'admin.roles.edit-permissions',
                                    'label' => 'Permission',
                                    'params' => ['role' => 'id'],
                                    'class' => 'text-green-500 hover:text-green-700 mr-3',
                                ],
                                'delete' => [
                                    'route' => 'admin.roles.destroy',
                                    'label' => 'Delete',
                                    'params' => ['role' => 'id'],
                                    'class' => 'text-red-600 hover:text-red-900',
                                    'method' => 'DELETE',
                                ],
                            ];
                        @endphp

                        <x-dynamic-table :headers="$headers"
                            :data="$data"
                            :actions="$actions"
                            :options="['wrapperClass' => 'border border-gray-200']" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
