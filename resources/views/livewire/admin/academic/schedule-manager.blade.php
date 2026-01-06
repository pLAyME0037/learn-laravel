<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    <!-- Toolbar -->
    <div
        class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6 flex flex-col md:flex-row gap-4 justify-between items-end">

        <div class="flex gap-4 flex-1 w-full">
            <!-- Semester -->
            <div class="w-1/4">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">
                    Semester
                </label>
                <select wire:model.live="semester_id"
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm text-sm">
                    @foreach ($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Day Filter -->
            <div class="w-1/4">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">
                    Filter Day
                </label>
                <select wire:model.live="filterDay"
                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm text-sm">
                    <option value="">All Days</option>
                    @foreach ($days as $key => $val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Instructor Filter -->
            <div class="w-1/4">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">
                    Instructor
                </label>
                <x-autocomplete-select :items="$instructors"
                    wire-model="filterInst"
                    placeholder="Type instructor name..."
                    wire:key="autocomplete-instructor" />
            </div>

            <!-- Search -->
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">
                    Search Course
                </label>
                <div class="relative">
                    <input type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Enter Code or Name..."
                        class="w-full pl-10 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <button wire:click="create"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow-md text-sm font-bold flex items-center transition-colors">
            <svg class="w-4 h-4 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4">
                </path>
            </svg>
            Schedule Class
        </button>
    </div>

    <!-- Table -->
    <div
        class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Time & Day</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Course Details</th>
                    <th
                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Instructor</th>
                    <th
                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Location</th>
                    <th
                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Seats</th>
                    <th
                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Status</th>
                    <th
                        class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                @forelse($sessions as $class)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <!-- Time -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $days[$class->day_of_week] ?? $class->day_of_week }}
                                </span>
                                <span class="text-xs font-mono text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($class->start_time)->format('g:i A') }} -
                                    {{ \Carbon\Carbon::parse($class->end_time)->format('g:i A') }}
                                </span>
                            </div>
                        </td>

                        <!-- Course -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $class->course->code }}
                                    <span class="text-gray-400 text-xs ml-1 font-normal">(Sec
                                        {{ $class->section_name }})</span>
                                </span>
                                <span class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">
                                    {{ $class->course->name }}
                                </span>
                            </div>
                        </td>

                        <!-- Instructor -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div
                                    class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs mr-3">
                                    <x-profile-image size="xs"
                                        src="{{ $class->instructor->profile_picture_url }}"
                                        alt="{{ $class->instructor->username ?? 'N/A' }}" />
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $class->instructor->name ?? 'Unassigned' }}</div>
                                    @if ($class->instructor)
                                        <!-- Assuming you want to show email or staff ID -->
                                        <div class="text-xs text-gray-500">
                                            {{ $class->instructor->email }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Location --}}
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            @if ($class->classroom)
                                <div class="font-bold text-gray-900 dark:text-white">
                                    {{ $class->classroom->room_number }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $class->classroom->building_name }}
                                </div>
                            @else
                                <span class="italic text-gray-400">TBA</span>
                            @endif
                        </td>

                        <!-- Capacity -->
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span
                                    class="text-sm font-bold {{ $class->enrolled_count >= $class->capacity ? 'text-red-600' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $class->enrolled_count }}
                                </span>
                                <span class="text-[10px] text-gray-400 border-t border-gray-300 w-full">
                                    of {{ $class->capacity }}
                                </span>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 text-center">
                            @php
                                $colors = [
                                    'open' => 'bg-green-100 text-green-800 border-green-200',
                                    'closed' => 'bg-red-100 text-red-800 border-red-200',
                                    'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                                ];
                                $color = $colors[$class->status] ?? $colors['open'];
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $color }}">
                                {{ ucfirst($class->status) }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <button wire:click="edit({{ $class->id }})"
                                class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 mr-4 font-semibold">
                                Edit
                            </button>
                            <button wire:click="delete({{ $class->id }})"
                                wire:confirm="Are you sure? This will delete the class schedule."
                                class="text-red-600 hover:text-red-900 dark:hover:text-red-400 font-semibold">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-lg font-medium">No classes found</span>
                            <p class="text-sm mt-1">Try adjusting the filters or adding a new class.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sessions->links() }}
    </div>

    <!-- MODAL -->
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
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="bg-white dark:bg-gray-800 px-6 pt-6 pb-4">
                        <h3
                            class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2 dark:border-gray-700">
                            {{ $isEditing ? 'Edit Class Session' : 'Schedule New Class' }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Course -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Select Course
                                </label>
                                <x-autocomplete-select :items="$courses"
                                    wire-model="course_id"
                                    placeholder="Type course code or name..."
                                    wire:key="autocomplete-course" />
                                @error('course_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Instructor -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Instructor
                                </label>
                                <x-autocomplete-select :items="$instructors"
                                    wire-model="instructor_id"
                                    placeholder="Type instructor name here..." />
                                @error('instructor_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Room
                                </label>
                                <x-autocomplete-select :items="$classrooms"
                                    wire-model="classroom_id"
                                    placeholder="Type classroom name here..." />
                                @error('classroom_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Section & Capacity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Section Name
                                </label>
                                <input type="text"
                                    wire:model="section_name"
                                    placeholder="e.g. A, B, 101"
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Max Capacity
                                </label>
                                <input type="number"
                                    wire:model="capacity"
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                            </div>

                            <!-- Schedule -->
                            <div
                                class="col-span-2 grid grid-cols-3 gap-4 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border dark:border-gray-700">
                                <div class="col-span-3 mb-1 text-xs font-bold uppercase text-gray-500">
                                    Time Slot
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">
                                        Day
                                    </label>
                                    <select wire:model="day_of_week"
                                        class="w-full text-sm rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                        @foreach ($days as $k => $v)
                                            <option value="{{ $k }}">{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">
                                        Start
                                    </label>
                                    <input type="time"
                                        wire:model="start_time"
                                        class="w-full text-sm rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">
                                        End
                                    </label>
                                    <input type="time"
                                        wire:model="end_time"
                                        class="w-full text-sm rounded-md border-gray-300 dark:bg-gray-900 dark:text-white">
                                </div>
                            </div>
                            @error('end_time')
                                <div class="col-span-2 text-red-500 text-xs">{{ $message }}</div>
                            @enderror

                            <!-- Status -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Status
                                </label>
                                <div class="flex gap-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio"
                                            wire:model="status"
                                            value="open"
                                            class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            Open
                                        </span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio"
                                            wire:model="status"
                                            value="closed"
                                            class="text-red-600 border-gray-300 focus:ring-red-500">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            Closed
                                        </span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio"
                                            wire:model="status"
                                            value="cancelled"
                                            class="text-gray-600 border-gray-300 focus:ring-gray-500">
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                            Cancelled
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex flex-row-reverse gap-3">
                        <button wire:click="save"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded shadow hover:bg-indigo-700 transition-colors">
                            Save Class
                        </button>
                        <button wire:click="$set('showModal', false)"
                            class="px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600">
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
