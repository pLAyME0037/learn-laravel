<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Semesters') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.semesters.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Add New Semester') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @php
                        $headers = [
                            'name' => 'Name',
                            'academic_year_name' => 'Academic Year',
                            'start_date' => 'Start Date',
                            'end_date' => 'End Date',
                            'is_current' => 'Current',
                        ];

                        $data = $semesters->map(function ($semester) {
                            return [
                                'name' => $semester->name,
                                'academic_year_name' => $semester->academicYear->name,
                                'start_date' => $semester->start_date->format('Y-m-d'),
                                'end_date' => $semester->end_date->format('Y-m-d'),
                                'is_current' => $semester->is_current ? 'Yes' : 'No',
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.semesters.show',
                                'label' => 'View',
                                'params' => ['semester' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.semesters.edit',
                                'label' => 'Edit',
                                'params' => ['semester' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.semesters.destroy',
                                'label' => 'Delete',
                                'params' => ['semester' => 'id'],
                                'class' => 'text-red-600 hover:text-red-900',
                                'method' => 'DELETE',
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
