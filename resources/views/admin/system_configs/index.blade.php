<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Configurations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.system-configs.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Add New System Configuration') }}
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
                            'key' => 'Key',
                            'value' => 'Value',
                            'description' => 'Description',
                            'created_at' => 'Created At',
                            'updated_at' => 'Updated At',
                        ];

                        $data = $systemConfigs->map(function ($config) {
                            return [
                                'id' => $config->id,
                                'key' => $config->key,
                                'value' => $config->value,
                                'description' => $config->description,
                                'created_at' => $config->created_at
                                    ? \Carbon\Carbon::parse($config->created_at)->format('d-m-Y H:i:s')
                                    : '',
                                'updated_at' => $config->updated_at
                                    ? \Carbon\Carbon::parse($config->updated_at)->format('d-m-Y H:i:s')
                                    : '',
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.system_configs.show',
                                'label' => 'View',
                                'params' => ['system_config'],
                            ],
                            'edit' => [
                                'route' => 'admin.system_configs.edit',
                                'label' => 'Edit',
                                'params' => ['system_config'],
                            ],
                            'delete' => [
                                'route' => 'admin.system_configs.destroy',
                                'label' => 'Delete',
                                'params' => ['system_config'],
                                'confirm' => 'Are you sure you want to delete this system configuration?',
                            ],
                        ];
                    @endphp

                    <x-dynamic-table :headers="$headers"
                        :data="$data"
                        :actions="$actions" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
