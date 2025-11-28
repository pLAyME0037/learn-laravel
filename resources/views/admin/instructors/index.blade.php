<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Instructors') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Manage Instructors
                        </h3>
                        <a href="{{ route('admin.instructors.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Create New Instructor
                        </a>
                    </div>

                    @php
                        $headers = [
                            'name' => 'Name',
                            'department_name' => 'Department',
                            'payscale' => 'Payscale',
                            'rank' => 'Rank',
                            'hire_date' => 'Hire Date',
                            'programs' => 'Programs',
                            'courses' => 'Courses Taught',
                            'actions' => 'Actions',
                        ];

                        $data = $instructors->map(function ($instructor) {
                            $programs = $instructor->courses->pluck('program.name')->filter()->unique()->implode(', ');
                            $courses = $instructor->courses->pluck('name')->filter()->implode(', ');

                            return [
                                'id' => $instructor->id,
                                'name' => $instructor->user->name,
                                'department_name' => $instructor->department->name ?? 'N/A',
                                'payscale' => $instructor->payscale,
                                'hire_date' => \Carbon\Carbon::parse($instructor->created_at)->format('Y-m-d'),
                                'programs' => $programs,
                                'courses' => $courses,
                            ];
                        });
                    @endphp

                    <x-table :headers="$headers"
                        :data="$data"
                        :options="['wrapperClass' => 'border border-gray-200']">

                        <x-slot name="bodyContent">
                            @forelse ($data as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    {{-- Render standard data columns dynamically --}}
                                    @foreach (array_keys($headers) as $key)
                                        @if ($key === 'programs' || $key === 'courses')
                                            <td
                                                class="px-6 py-4 whitespace-normal text-sm text-gray-900 dark:text-gray-100">
                                                {{ data_get($row, $key) ?? '' }}
                                            </td>
                                        @elseif ($key !== 'actions')
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ data_get($row, $key) ?? '' }}
                                            </td>
                                        @endif
                                    @endforeach

                                    {{-- Render the actions column --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                        <div class="flex items-center space-x-4">
                                            <x-table.action :action="[
                                                'type' => 'link',
                                                'label' => 'Show',
                                                'route' => 'admin.instructors.show',
                                                'params' => ['instructor' => 'id'],
                                                'class' => 'text-gray-500 hover:text-gray-700',
                                            ]"
                                                :row="$row" />
                                            @canany(['edit.instructors', 'create.instructors'])
                                                <x-table.action :action="[
                                                    'type' => 'link',
                                                    'label' => 'Edit',
                                                    'route' => 'admin.instructors.edit',
                                                    'params' => ['instructor' => 'id'],
                                                ]"
                                                    :row="$row" />
                                            @endcanany
                                            @can('edit.instructors')
                                                <x-table.action :action="[
                                                    'type' => 'delete',
                                                    'label' => 'Delete',
                                                    'route' => 'admin.instructors.destroy',
                                                    'params' => ['instructor' => 'id'],
                                                ]"
                                                    :row="$row" />
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($headers) }}"
                                        class="px-6 py-4 text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            No instructors available.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </x-slot>
                    </x-table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
