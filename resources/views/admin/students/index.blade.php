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
        <!-- Filters with alpine state wrapper -->
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
                $filters = [
                    [
                        'type' => 'text',
                        'name' => 'search',
                        'label' => 'Search',
                        'value' => $filters['search'] ?? '',
                        'placeholder' => 'id, name, email, or username',
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
                        'onChange' => "progId = ''", // Reset when changed
                        'errorMessages' => $errors->get('department_id'),
                    ],
                    [
                        'type' => 'select',
                        'name' => 'program_id',
                        'label' => 'Program',
                        'defaultOptionText' => 'All Programs',
                        'alpine_model' => 'progId',
                        'alpine_options' => 'filteredPrograms', // Js getter name
                        'option_value' => 'id', // key in JSON object
                        'option_text' => 'name', // key in JSON object
                        'errorMessages' => $errors->get('program_id'),
                    ],
                    [
                        'type' => 'select',
                        'name' => 'academic_status',
                        'label' => 'Academic Status',
                        'options' => [
                            ['value' => 'active', 'text' => 'Active'],
                            ['value' => 'probation', 'text' => 'Probation'],
                            ['value' => 'graduated', 'text' => 'Graduated'],
                            ['value' => 'suspended', 'text' => 'Suspended'],
                            ['value' => 'withdrawn', 'text' => 'Withdrawn'],
                            ['value' => 'transfered', 'text' => 'Transfered'],
                            ['value' => 'trashed', 'text' => 'Trashed'],
                        ],
                        'selectedValue' => $filters['academic_status'] ?? '',
                        'defaultOptionText' => 'All Status',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'enrollment_status',
                        'label' => 'Enrollment Status',
                        'options' => [
                            ['value' => 'full_time', 'text' => 'Full Time'],
                            ['value' => 'part_time', 'text' => 'Part Time'],
                            ['value' => 'exchange', 'text' => 'Exchange'],
                            ['value' => 'study_abroad', 'text' => 'Study Aboard'],
                        ],
                        'selectedValue' => $filters['enrollment_status'] ?? '',
                        'defaultOptionText' => 'All Status',
                    ],
                ];
            @endphp
            <x-filter-box :action="route('admin.students.index')"
                :filters="$filters"
                uri="admin.students.create"
                createBtn="Add New Student" />
        </div>

        @php
            $headers = [
                'Student Indentity', // pocture, name, id, email
                'Department & Program',
                'Status',
                'Admission Date',
                'Year',
                'Action',
            ];
        @endphp

        <x-table :header="$headers"
            :data="$students">

            <x-slot name='bodyContent'>
                @forelse ($students as $student)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-4 whitespace-nowrap">
                            {{-- Student Identity --}}
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <x-profile-image class="md"
                                        src="{{ $student->user?->profile_picture_url ?? asset('images/default-profile.png') }}"
                                        alt="{{ $student->user?->name ?? 'N/A' }}" />
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        ID:{{ $student->student_id }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        Name:{{ $student->user?->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <span>{{ $student->user?->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        {{-- Department & Program --}}
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div>{{ $student->department?->name ?? 'N/A' }} /</div>
                            <div>{{ $student->program?->name ?? 'N/A' }}</div>
                        </td>
                        {{-- Status --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student->academic_status_classes }}">
                                {{ ucfirst($student->academic_status ?? 'Unknown') }}
                            </span>
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">
                                {{ Str::ucfirst(str_replace('_', ' ', $student->enrollment_status ?? 'Unknown')) }}
                            </span>
                        </td>
                        {{-- Admission --}}
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $student->admission_date?->format('M d, Y') ?? 'N/A' }}
                        </td>
                        {{-- Year --}}
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $student->year_level_name ?? 'N/A' }}
                        </td>
                        {{-- Action --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if ($student->trashed())
                                    @can('restore.students')
                                        <x-table.action :action="[
                                            'type' => 'post-button',
                                            'label' => 'Restore',
                                            'route' => 'admin.students.restore',
                                            'params' => ['student' => 'id'],
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}"
                            class="px-6 py-4 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                No Students found.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </x-slot>

            <x-slot name='pagination'>
                {{ $students->links() }}
            </x-slot>
        </x-table>
    </div>
</x-app-layout>
