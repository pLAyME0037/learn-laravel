<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Faculties') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Manage Faculties
                        </h3>
                        <a href="{{ route('admin.faculties.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Create New Faculty
                        </a>
                    </div>

                    @php
                        $headers = [
                            'id' => 'Faculty ID',
                            'faculty_name' => 'Faculty Name',
                            'dept_name' => 'Department',
                        ];

                        $data = $faculties->map(function ($faculty) {
                            return [
                                'id'=> $faculty->id,
                                'faculty_name' => $faculty->name,
                                'dept_name' => $faculty->departments->pluck('name')->implode('<br>'),
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.faculties.show',
                                'label' => 'View',
                                'params' => ['faculty' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.faculties.edit',
                                'label' => 'Edit',
                                'params' => ['faculty' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.faculties.destroy',
                                'label' => 'Delete',
                                'params' => ['faculty' => 'id'],
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
