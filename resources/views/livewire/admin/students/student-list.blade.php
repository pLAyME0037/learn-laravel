<div>
    <div class="py-3 max-w-full mx-auto sm:px-6 lg:px-8">

        {{-- FILTER SECTION --}}
        @php
            $filtersConfig = [
                [
                    'type' => 'text',
                    'name' => 'search', // Matches public $search in Component
                    'label' => 'Search',
                    'placeholder' => 'ID, Name, Email...',
                ],
                [
                    'type' => 'select',
                    'name' => 'filterDepartment', // Matches public $filterDepartment
                    'label' => 'Department',
                    // We pass the raw array for the loop
                    'options' => $departments->map(fn($d) => ['value' => $d->id, 'text' => $d->name])->toArray(),
                    'defaultOptionText' => 'All Departments',
                ],
                [
                    'type' => 'select',
                    'name' => 'filterProgram', // Matches public $filterProgram
                    'label' => 'Program',
                    // This list automatically updates when Department changes via Livewire lifecycle
                    'options' => $programs->map(fn($p) => ['value' => $p->id, 'text' => $p->name])->toArray(),
                    'defaultOptionText' => 'All Programs',
                ],
                [
                    'type' => 'select',
                    'name' => 'filterStatus', // Matches public $filterStatus
                    'label' => 'Status',
                    'options' => [
                        ['value' => 'active', 'text' => 'Active'],
                        ['value' => 'probation', 'text' => 'Probation'],
                        ['value' => 'graduated', 'text' => 'Graduated'],
                        ['value' => 'suspended', 'text' => 'Suspended'],
                    ],
                    'defaultOptionText' => 'All Status',
                ],
            ];
            // Table Columns Definition
            $columns = [
                ['key' => 'student_id', 'label' => 'Student Identity', 'align' => 'left'],
                ['key' => 'department', 'label' => 'Program / Department', 'align' => 'center', 'width' => 'w-1/4'],
                ['key' => 'address', 'label' => 'Address', 'align' => 'left', 'width' => 'w-1/4'],
                ['key' => 'status', 'label' => 'Status', 'align' => 'center'],
                ['key' => 'term', 'label' => 'Progress', 'align' => 'center'],
            ];
        @endphp

        <x-filter-live :filters="$filtersConfig"
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
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600">
                    </x-table.cell>

                    {{-- 1. IDENTITY --}}
                    <x-table.cell>
                        <div class="flex items-center">
                            <!-- Profile Pic -->
                            <div class="">
                                <x-profile-image size="sm"
                                    src="{{ $student->user?->profile_picture_url }}"
                                    alt="{{ $student->user?->name ?? 'N/A' }}" />
                            </div>

                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $student->user->name }}
                                </div>
                                <div class="text-xs text-indigo-600 dark:text-indigo-400 font-mono">
                                    {{ $student->student_id }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $student->user->email }}
                                </div>
                            </div>
                        </div>
                    </x-table.cell>

                    {{-- 2. DEPT / PROGRAM --}}
                    <x-table.cell class="whitespace-normal">
                        <div class="text-sm text-gray-900 dark:text-white font-medium">
                            {{ $student->program->name ?? 'Unassigned' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $student->program->major->department->name ?? 'N/A' }}
                        </div>
                    </x-table.cell>

                    {{-- 3. ADDRESS --}}
                    <x-table.cell class="whitespace-normal">
                        @if ($student->address)
                            <div class="text-xs text-gray-600 dark:text-gray-300">
                                {{ $student->address->current_address }}
                            </div>
                            @if ($student->address->village)
                                <div class="text-[10px] text-gray-400 mt-1">
                                    <p>
                                        <span class="text-orange-400">ភូមិ</span>
                                        {{ ' ' . $student->address->village->name_kh }}
                                    </p>
                                    <p>
                                        <span class="text-orange-400">ស្រុក</span>
                                        {{ ' ' . $student->address->village->commune->name_kh }}
                                    </p>
                                    <p>
                                        <span class="text-orange-400">ឃុំ​-សង្កាត់់</span>
                                        {{ ' ' . $student->address->village->commune->district->name_kh }}
                                    </p>
                                    <p>
                                        <span class="text-orange-400">ខេត្ត-ក្រុង</span>
                                        {{ ' ' . $student->address->village->commune->district->province->name_kh }}
                                    </p>
                                </div>
                            @endif
                        @else
                            <span class="text-xs text-gray-400 italic">
                                No address
                            </span>
                        @endif
                    </x-table.cell>

                    {{-- 4. STATUS --}}
                    <x-table.cell class="text-center">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $student->academic_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($student->academic_status) }}
                        </span>
                    </x-table.cell>

                    {{-- 5. TERM/PROGRESS --}}
                    <x-table.cell class="text-center">
                        <div class="text-[12px] text-gray-400 mt-1">
                            Year {{ ceil($student->current_term / 2) }}
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            Semester {{ $student->current_term % 2 == 0 ? 2 : 1 }}
                        </span>
                    </x-table.cell>

                    {{-- ACTIONS --}}
                    <x-table.cell class="text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.students.show', $student->id) }}"
                                class="text-gray-500 hover:text-indigo-600">
                                <svg class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.students.edit', $student->id) }}"
                                class="text-gray-500 hover:text-blue-600">
                                <svg class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </a>
                            <button wire:click="delete({{ $student->id }})"
                                wire:confirm="Are you sure?"
                                class="text-gray-500 hover:text-red-600">
                                <svg class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </x-table.cell>

                </x-table.row>
            @empty
                <tr>
                    <td colspan="6"
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
                                No students found
                            </span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-data-table>
</div>
