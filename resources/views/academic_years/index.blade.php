<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Academic Years') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.academic_years.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Academic Year
                        </a>
                    </div>

                    @php
                        $headers = [
                            'name' => 'Name',
                            'start_date' => 'Start Date',
                            'end_date' => 'End Date',
                            'is_current' => 'Current',
                        ];
                        $data = $academicYears->map(function ($year) {
                            return [
                                'id' => $year->id,
                                'name' => $year->name,
                                'start_date' => $year->start_date,
                                'end_date' => $year->end_date,
                                'is_current' => $year->is_current ? 'Yes' : 'No',
                            ];
                        });
                        $actions = [
                            [
                                'label' => 'View',
                                'route' => 'admin.academic_years.show',
                                'class' =>
                                    'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-600',
                            ],
                            [
                                'label' => 'Edit',
                                'route' => 'admin.academic_years.edit',
                                'class' =>
                                    'text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600',
                            ],
                            [
                                'label' => 'Delete',
                                'route' => 'admin.academic_years.destroy',
                                'method' => 'DELETE',
                                'class' => 'text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600',
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
