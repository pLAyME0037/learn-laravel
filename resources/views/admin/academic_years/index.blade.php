<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Academic Years') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-400">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200">
                        Manage Academic Years
                    </h3>
                    <a href="{{ route('admin.academic-years.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Add New Academic Year') }}
                    </a>
                </div>

                <div class="mb-4">
                    <input type="text"
                        placeholder="Search academic years..."
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                </div>

                @php
                    $headers = [
                        'name' => 'Acdemic',
                        'start_date' => 'Start Date',
                        'end_date' => 'End Date',
                        'is_current' => 'Current',
                    ];

                    $data = $academicYears->map(function ($academicYear) {
                        return [
                            'id' => $academicYear->id,
                            'name' => $academicYear->name,
                            'start_date' => $academicYear->start_date,
                            'end_date' => $academicYear->end_date,
                            'is_current' => $academicYear->is_current ? 'Yes' : 'No',
                        ];
                    });

                    $actions = [
                        'view' => [
                            'route' => 'admin.academic-years.show',
                            'label' => 'View',
                            'params' => ['academic_year' => 'id'],
                            'class' => 'text-gray-400 hover:text-gray-500',
                        ],
                        'edit' => [
                            'route' => 'admin.academic-years.edit',
                            'label' => 'Edit',
                            'params' => ['academic_year' => 'id'],
                            'class' => 'text-indigo-600 hover:text-indigo-900',
                        ],
                        'delete' => [
                            'route' => 'admin.academic-years.destroy',
                            'label' => 'Delete',
                            'params' => ['academic_year' => 'id'],
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
                    {{ $academicYears->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
