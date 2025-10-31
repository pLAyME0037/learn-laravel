<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Classrooms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Manage Classrooms</h3>
                        <a href="{{ route('admin.classrooms.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add New Classroom') }}
                        </a>
                    </div>

                    <div class="mb-4">
                        <input type="text" placeholder="Search classrooms..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>

                    @php
                        $headers = [
                            'name' => 'Name',
                            'capacity' => 'Capacity',
                            'type' => 'Type',
                            'location' => 'Location',
                        ];

                        $data = $classrooms->map(function ($classroom) {
                            return [
                            'name' => "$classroom->name",
                            'capacity' => "$classroom->capacity",
                            'type' => "$classroom->type",
                            'location' => "$classroom->location",
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.classrooms.show',
                                'label' => 'View',
                                'params' => ['classroom' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.classrooms.edit',
                                'label' => 'Edit',
                                'params' => ['classroom' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.classrooms.destroy',
                                'label' => 'Delete',
                                'params' => ['classroom' => 'id'],
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
                        {{ $classrooms->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
