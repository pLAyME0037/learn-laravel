<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Students Management') }}
            </h2>
            @can('students.create')
                <a href="{{ route('admin.students.create') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Add New Student') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- FILTER SECTION --}}
        <div x-data="{
            deptId: '{{ $filters['department_id'] ?? '' }}',
            progId: '{{ $filters['program_id'] ?? '' }}',
            allPrograms: {{ \Illuminate\Support\Js::from($programs) }},
            get filteredPrograms() {
                if (!this.deptId) return this.allPrograms;
                return this.allPrograms.filter(p => p.department_id == this.deptId)
            }
        }">
            @php
                $filtersConfig = [
                    [
                        'type' => 'text',
                        'name' => 'search',
                        'label' => 'Search',
                        'value' => $filters['search'] ?? '',
                        'placeholder' => 'ID, Name, Email...',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'department_id',
                        'label' => 'Department',
                        'options' => $departments
                            ->map(fn($dept) => ['value' => $dept->id, 'text' => $dept->name])
                            ->toArray(),
                        'selectedValue' => $filters['department_id'] ?? '',
                        'defaultOptionText' => 'All Departments',
                        'alpine_model' => 'deptId',
                        'onChange' => "progId = ''",
                    ],
                    [
                        'type' => 'select',
                        'name' => 'program_id',
                        'label' => 'Program',
                        'defaultOptionText' => 'All Programs',
                        'alpine_model' => 'progId',
                        'alpine_options' => 'filteredPrograms',
                        'option_value' => 'id',
                        'option_text' => 'name',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'academic_status',
                        'label' => 'Status',
                        'options' => [
                            ['value' => 'active', 'text' => 'Active'],
                            ['value' => 'probation', 'text' => 'Probation'],
                            ['value' => 'graduated', 'text' => 'Graduated'],
                            ['value' => 'suspended', 'text' => 'Suspended'],
                            ['value' => 'trashed', 'text' => 'Trashed'],
                        ],
                        'selectedValue' => $filters['academic_status'] ?? '',
                        'defaultOptionText' => 'All Status',
                    ],
                ];

                // TABLE COLUMNS CONFIGURATION
                $columns = [
                    ['key' => 'student_id', 'label' => 'Student Identity', 'align' => 'left'],
                    ['key' => 'department', 'label' => 'Department / Program', 'align' => 'left', 'width' => 'w-1/4'],
                    ['key' => 'address', 'label' => 'Address', 'align' => 'left', 'width' => 'w-1/4'],
                    ['key' => 'status', 'label' => 'Status', 'align' => 'center'],
                    ['key' => 'admission', 'label' => 'Admission', 'align' => 'left'],
                    ['key' => 'year', 'label' => 'Year', 'align' => 'center'],
                ];
            @endphp

            <x-filter-box :action="route('admin.students.index')"
                :filters="$filtersConfig"
                uri="admin.students.create"
                createBtn="Add New Student" />
        </div>

        {{-- DATA TABLE --}}
        <x-data-table :rows="$students"
            :columns="$columns"
            :selectable="true"
            :with-actions="true">
            <x-slot name="body">
                @forelse ($students as $student)
                    <x-table.row wire:key="row-{{ $student->id }}"
                        :item-id="$student->id">

                        {{-- CHECKBOX --}}
                        <x-table.cell class="w-4">
                            <input type="checkbox"
                                value="{{ $student->id }}"
                                x-model="selected"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600">
                        </x-table.cell>

                        {{-- 1. IDENTITY --}}
                        <x-table.cell>
                            <div class="flex items-center">
                                <x-profile-image class="md"
                                    src="{{ $student->user?->profile_picture_url }}"
                                    alt="{{ $student->user?->name ?? 'N/A' }}" />
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <span class="text-blue-200">ID:</span>
                                        {{ $student->student_id }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <span class="text-blue-200">Name:</span>
                                        {{ $student->full_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $student->email ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </x-table.cell>

                        {{-- 2. DEPT / PROGRAM --}}
                        <x-table.cell class="whitespace-normal">
                            <div class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ $student->department?->name ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $student->program?->name ?? '-' }}
                            </div>
                        </x-table.cell>

                        {{-- 3. ADDRESS --}}
                        <x-table.cell class="whitespace-normal">
                            <x-address-block :student="$student" />
                        </x-table.cell>

                        {{-- 4. STATUS --}}
                        <x-table.cell class="text-center">
                            <div class="flex flex-col gap-1 items-center">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student->academic_status_classes }}">
                                    {{ ucfirst($student->academic_status) }}
                                </span>
                                <span
                                    class="px-2 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    {{ Str::ucfirst(str_replace('_', ' ', $student->enrollment_status)) }}
                                </span>
                            </div>
                        </x-table.cell>

                        {{-- 5. ADMISSION --}}
                        <x-table.cell>
                            {{ $student->admission_date?->format('M d, Y') ?? 'N/A' }}
                        </x-table.cell>

                        {{-- 6. YEAR --}}
                        <x-table.cell class="text-center">
                            {{ $student->year_level_name ?? 'N/A' }}
                        </x-table.cell>

                        {{-- ACTIONS --}}
                        <x-table.cell class="text-right">
                            <div class="flex items-center justify-end space-x-2">
                                @if ($student->trashed())
                                    @can('restore.students')
                                        <x-table.action :action="[
                                            'type' => 'post-button',
                                            'label' => 'Restore',
                                            'route' => 'admin.students.restore',
                                            'params' => ['student' => 'id'],
                                            '_method' => 'PUT',
                                            'class' => 'text-green-600',
                                        ]"
                                            :row="$student" />
                                    @endcan
                                    @can('force-delete.students')
                                        <x-table.action :action="[
                                            'type' => 'delete',
                                            'label' => 'Delete',
                                            'route' => 'admin.students.force-delete',
                                            'params' => ['student' => 'id'],
                                        ]"
                                            :row="$student" />
                                    @endcan
                                @else
                                    @can('view.students')
                                        <x-table.action :action="[
                                            'type' => 'link',
                                            'label' => 'Show',
                                            'route' => 'admin.students.show',
                                            'params' => ['student' => 'id'],
                                            'class' => 'text-gray-500',
                                        ]"
                                            :row="$student" />
                                    @endcan
                                    @can('edit.students')
                                        <x-table.action :action="[
                                            'type' => 'link',
                                            'label' => 'Edit',
                                            'route' => 'admin.students.edit',
                                            'params' => ['student' => 'id'],
                                        ]"
                                            :row="$student" />
                                    @endcan
                                    @can('delete.students')
                                        <x-table.action :action="[
                                            'type' => 'delete',
                                            'label' => 'Trash',
                                            'route' => 'admin.students.destroy',
                                            'params' => ['student' => 'id'],
                                        ]"
                                            :row="$student" />
                                    @endcan
                                @endif
                            </div>
                        </x-table.cell>

                    </x-table.row>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 2 }}"
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
                                <span class="text-lg font-medium">No students found</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </x-slot>
        </x-data-table>
    </div>
</x-app-layout>
