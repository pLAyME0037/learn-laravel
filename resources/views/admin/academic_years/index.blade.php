<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Academic Years') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-400">
                <div class="flex justify-end mb-4">
                    <a href="{{ route('admin.academic-years.create') }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('Add New Academic Year') }}
                    </a>
                </div>

                @php
                    $headers = [
                        [
                            'label' => 'No',
                            'align' => 'center',
                        ],
                        [
                            'key' => 'name',
                            'label' => 'Academic Year Name',
                            'align' => 'center',
                        ],
                        [
                            'key' => 'start_date',
                            'label' => 'Start Date',
                            'align' => 'center',
                        ],
                        [
                            'key' => 'end_date',
                            'label' => 'End Date',
                            'align' => 'center',
                        ],
                        [
                            'key' => 'is_current',
                            'label' => 'Current',
                            'align' => 'center',
                        ],
                    ];
                @endphp

                <x-data-table :rows="$academicYears"
                    :columns="$headers"
                    :selectable="true"
                    :with-actions="true"
                    :sort-col="request('orderby')"
                    :sort-dir="request('direction', 'asc')">

                    <x-slot name="body">
                        @forelse ($academicYears as $academicYear)
                            <x-table.row wire:key="row-{{ $academicYear->id }}"
                                :item-id="$academicYear->id">
                                {{-- CHECKBOX --}}
                                <x-table.cell class="w-4">
                                    <input type="checkbox"
                                        value="{{ $academicYear->id }}"
                                        x-model="selected"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600">
                                </x-table.cell>
                                <x-table.cell>
                                    {{ $loop->iteration }}
                                </x-table.cell>
                                <x-table.cell>
                                    {{ $academicYear->name }}
                                </x-table.cell>
                                <x-table.cell>
                                    {{ $academicYear->start_date->format('d-M-Y') }}
                                </x-table.cell>
                                <x-table.cell>
                                    {{ $academicYear->end_date->format('d-M-Y') }}
                                </x-table.cell>
                                <x-table.cell>
                                    {{ $academicYear->is_current ? 'Yes' : 'No' }}
                                </x-table.cell>
                                <x-table.cell>
                                    <div class="flex items-center justify-end space-x-2">
                                        <x-table.action :action="[
                                            'type' => 'link',
                                            'label' => 'View',
                                            'route' => 'admin.academic-years.show',
                                            'params' => ['academic_year' => 'id'],
                                            'class' => 'text-green-600',
                                        ]"
                                            :row="$academicYear" />
                                        <x-table.action :action="[
                                            'type' => 'link',
                                            'label' => 'Edit',
                                            'route' => 'admin.academic-years.edit',
                                            'params' => ['academic_year' => 'id'],
                                        ]"
                                            :row="$academicYear" />
                                        <x-table.action :action="[
                                            'type' => 'delete',
                                            'label' => 'Delete',
                                            'route' => 'admin.academic-years.destroy',
                                            'params' => ['academic_year' => 'id'],
                                        ]"
                                            :row="$academicYear" />
                                    </div>
                                </x-table.cell>
                            </x-table.row>
                        @empty
                            <tr>
                                <td colspan="{{ count($headers) + 2 }}"
                                    class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                            </path>
                                        </svg>
                                        <span class="text-lg font-medium">
                                            No academic years found
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </x-slot>
                </x-data-table>

                <div class="mt-4">
                    {{ $academicYears->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
