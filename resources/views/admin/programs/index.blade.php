<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Programs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Manage Programs</h3>
                        <a href="{{ route('admin.programs.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add New Program') }}
                        </a>
                    </div>

                    <div class="mb-4">
                        <input type="text"
                            placeholder="Search programs..."
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>

                    @php
                        $headers = [
                            'name' => 'Name',
                            'code' => 'Code',
                            'department_name' => 'Department',
                            'degree_name' => 'Degree',
                            'duration_years' => 'Duration (Years)',
                            'is_active' => 'Status',
                        ];

                        $data = $programs->map(function ($program) {
                            return [
                                'id' => "$program->id",
                                'name' => $program->name,
                                'code' => $program->code,
                                'department_name' => $program->department->name ?? 'N/A',
                                'degree_name' => $program->degree->name ?? 'N/A',
                                'duration_years' => $program->duration_years,
                                'is_active' => $program->is_active ? 'Active' : 'Inactive',
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.programs.show',
                                'label' => 'View',
                                'params' => ['program' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.programs.edit',
                                'label' => 'Edit',
                                'params' => ['program' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.programs.destroy',
                                'label' => 'Delete',
                                'params' => ['program' => 'id'],
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
                        {{ $programs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
