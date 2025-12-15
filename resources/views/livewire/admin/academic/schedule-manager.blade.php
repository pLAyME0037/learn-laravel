<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Notifications -->
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Bar -->
        <div
            class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex gap-4 flex-1">
                <!-- Semester Filter -->
                <div class="w-48">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                        Semester
                    </label>
                    <select wire:model.live="semester_id"
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm">
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Day Filter -->
                <div class="w-32">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Day</label>
                    <select wire:model.live="filterDay"
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm">
                        <option value="">All Days</option>
                        @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                            <option value="{{ $day }}">{{ $day }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search -->
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Search Course</label>
                    <input type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Code or Name..."
                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm text-sm">
                </div>
            </div>

            <button wire:click="create"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow text-sm font-bold flex items-center">
                <span class="mr-2 text-lg">+</span> Add Class
            </button>
        </div>

        <!-- Schedule Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Day / Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Instructor</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Capacity</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($sessions as $class)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $class->day_of_week }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                    {{ $class->course->code }}</div>
                                <div class="text-xs text-gray-500">{{ $class->course->name }} (Sec
                                    {{ $class->section_name }})</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $class->instructor->name ?? 'TBA' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-mono bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded">
                                    {{ $class->enrolled_count }} / {{ $class->capacity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-2 py-1 text-xs font-bold rounded-full {{ $class->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($class->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <button wire:click="edit({{ $class->id }})"
                                    class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <button wire:click="delete({{ $class->id }})"
                                    wire:confirm="Delete this class session?"
                                    class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No classes found for this selection.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t dark:border-gray-700">
                {{ $sessions->links() }}
            </div>
        </div>

        <!-- Create/Edit Modal -->
        @if ($showModal)
            <div class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                        wire:click="$set('showModal', false)"></div>
                    <div
                        class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                {{ $isEditing ? 'Edit Class' : 'Schedule New Class' }}
                            </h3>

                            <div class="space-y-4">
                                <!-- Course -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Course</label>
                                    <select wire:model="course_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                        <option value="">Select Course</option>
                                        @foreach ($courses as $id => $code)
                                            <option value="{{ $id }}">{{ $code }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Instructor -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instructor</label>
                                    <select wire:model="instructor_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                        <option value="">Select Instructor</option>
                                        @foreach ($instructors as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Section -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Section</label>
                                        <input type="text"
                                            wire:model="section_name"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <!-- Capacity -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capacity</label>
                                        <input type="number"
                                            wire:model="capacity"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <!-- Day -->
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Day</label>
                                        <select wire:model="day_of_week"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $d)
                                                <option value="{{ $d }}">{{ $d }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- Start -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start
                                            Time</label>
                                        <input type="time"
                                            wire:model="start_time"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <!-- End -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End
                                            Time</label>
                                        <input type="time"
                                            wire:model="end_time"
                                            class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                    </div>
                                </div>
                                @error('end_time')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror

                                <!-- Status -->
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <select wire:model="status"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                        <option value="open">Open</option>
                                        <option value="closed">Closed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button wire:click="save"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Save
                            </button>
                            <button wire:click="$set('showModal', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
