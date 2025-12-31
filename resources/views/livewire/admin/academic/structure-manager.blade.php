<div class="py-0 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Tabs Navigation -->
    <div class="border-b border-gray-200 dark:border-gray-700 mb-6 flex overflow-x-auto space-x-1">
        @foreach (['faculties', 'departments', 'degrees', 'majors', 'programs'] as $tab)
            <button wire:click="$set('activeTab', '{{ $tab }}')"
                class="px-6 py-3 font-medium text-sm focus:outline-none transition-colors 
                {{ $activeTab === $tab
                    ? 'bg-white dark:bg-gray-800 border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 shadow-sm'
                    : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                {{ ucfirst($tab) }}
            </button>
        @endforeach
    </div>

    <!-- FILTER SECTION (Dynamic based on Tab) -->
    @php
        $filtersConfig = [
            [
                'type' => 'text',
                'name' => 'search',
                'label' => 'Search Name',
                'placeholder' => 'Type to search...',
            ],
        ];

        // Add Specific Filters per Tab
        if ($activeTab === 'departments') {
            $filtersConfig[] = [
                'type' => 'select',
                'name' => 'filterFaculty',
                'label' => 'Faculty',
                'options' => $faculties_list->map(fn($f) => ['value' => $f->id, 'text' => $f->name])->toArray(),
                'defaultOptionText' => 'All Faculties',
            ];
        }

        if (in_array($activeTab, ['majors', 'programs'])) {
            $filtersConfig[] = [
                'type' => 'select',
                'name' => 'filterDepartment',
                'label' => 'Department',
                'options' => $departments_list->map(fn($d) => ['value' => $d->id, 'text' => $d->name])->toArray(),
                'defaultOptionText' => 'All Departments',
            ];
        }

        if (in_array($activeTab, ['majors', 'programs'])) {
            $filtersConfig[] = [
                'type' => 'select',
                'name' => 'filterDegree',
                'label' => 'Degree Level',
                'options' => $degrees_list->map(fn($d) => ['value' => $d->id, 'text' => $d->name])->toArray(),
                'defaultOptionText' => 'All Levels',
            ];
        }
    @endphp

    <x-filter-live :filters="$filtersConfig" />

    <!-- Action Bar (Create Button) -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">
            {{ ucfirst($activeTab) }} List
        </h2>
        <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow flex items-center">
            <span class="mr-2 text-lg">+</span>
             Add {{ ucfirst(substr($activeTab, 0, -1)) }}
        </button>
    </div>

    <!-- Main Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        No
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        Name
                    </th>

                    @if ($activeTab === 'departments')
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Code
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Faculty
                        </th>
                    @endif

                    @if ($activeTab === 'majors')
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Department
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Degree
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Cost
                        </th>
                    @endif

                    @if ($activeTab === 'programs')
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Major
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Degree
                        </th>
                    @endif

                    <th
                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($data as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                            {{ $data->firstItem() + $loop->index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                            {{ $item->name }}
                        </td>

                        @if ($activeTab === 'departments')
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                {{ $item->code }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $item->faculty->name ?? 'N/A' }}
                            </td>
                        @endif

                        @if ($activeTab === 'majors')
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $item->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $item->degree->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                ${{ number_format($item->cost_per_term, 2) }}
                            </td>
                        @endif

                        @if ($activeTab === 'programs')
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $item->major->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $item->degree->name ?? 'N/A' }}
                            </td>
                        @endif

                        <td class="px-6 py-4 text-right text-sm font-medium">
                            @if ($activeTab === 'programs')
                                <a href="{{ route('admin.programs.curriculum', $item->id) }}"
                                    class="text-green-600 hover:text-green-900 dark:hover:text-green-400 mr-3 font-bold">
                                    Plan
                                </a>
                            @endif
                            <button wire:click="edit({{ $item->id }})"
                                class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 mr-3">
                                Edit
                            </button>
                            <button wire:click="delete({{ $item->id }})"
                                wire:confirm="Are you sure? Items linked to this will be affected."
                                class="text-red-600 hover:text-red-900 dark:hover:text-red-400">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t dark:border-gray-700">
            {{ $data->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    wire:click="$set('showModal', false)">
                </div>

                <div
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white"
                            id="modal-title">
                            {{ $isEditing ? 'Edit' : 'Create' }} {{ ucfirst(substr($activeTab, 0, -1)) }}
                        </h3>

                        <div class="mt-4 space-y-4">

                            <!-- Fields for FACULTIES or DEGREES -->
                            @if (in_array($activeTab, ['faculties', 'degrees']))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Name
                                    </label>
                                    <input type="text"
                                        wire:model="formData.name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    @error('formData.name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <!-- Fields for DEPARTMENTS -->
                            @if ($activeTab === 'departments')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Faculty
                                    </label>
                                    <select wire:model="formData.faculty_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        <option value="">Select Faculty</option>
                                        @foreach ($faculties_list as $faculty)
                                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('formData.faculty_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Name
                                    </label>
                                    <input type="text"
                                        wire:model="formData.name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    @error('formData.name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Code (e.g.CS)
                                    </label>
                                    <input type="text"
                                        wire:model="formData.code"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    @error('formData.code')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <!-- Fields for MAJORS -->
                            @if ($activeTab === 'majors')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Department
                                    </label>
                                    <select wire:model="formData.department_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        <option value="">Select Department</option>
                                        @foreach ($departments_list as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('formData.department_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Degree Level
                                    </label>
                                    <select wire:model="formData.degree_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        <option value="">Select Degree</option>
                                        @foreach ($degrees_list as $degree)
                                            <option value="{{ $degree->id }}">{{ $degree->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('formData.degree_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Name
                                    </label>
                                    <input type="text"
                                        wire:model="formData.name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    @error('formData.name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Cost Per Term
                                    </label>
                                    <input type="number"
                                        wire:model="formData.cost_per_term"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    @error('formData.cost_per_term')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <!-- Fields for PROGRAMS -->
                            @if ($activeTab === 'programs')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Major
                                    </label>
                                    <select wire:model.live="formData.major_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        <option value="">Select Major</option>
                                        @foreach ($majors_list as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('formData.major_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Program Name
                                    </label>
                                    <input type="text"
                                        wire:model="formData.name"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                    <p class="text-xs text-gray-500 mt-1">
                                        Full official name (e.g. Bachelor of Science in CS)
                                    </p>
                                    @error('formData.name')
                                        <span class="text-red-500 text-xs">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            @endif

                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="save"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Save
                        </button>
                        <button wire:click="$set('showModal', false)"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@push('scripts')
    <x-sweet-alert />
@endpush
