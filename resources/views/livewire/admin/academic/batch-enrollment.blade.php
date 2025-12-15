<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- LEFT: Select Cohort -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold mb-4 dark:text-white">
                    1. Select Student in Class
                </h3>

                <div class="space-y-4">
                    <div>
                        <x-input-label>Department</x-input-label>
                        <select wire:model.live="department_id"
                            class="w-full border-gray-300 rounded-md dark:bg-gray-900 dark:text-white">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label>Program</x-input-label>
                        <select wire:model.live="program_id"
                            class="w-full border-gray-300 rounded-md dark:bg-gray-900 dark:text-white">
                            <option value="">Select Program</option>
                            @foreach ($programs as $prog)
                                <option value="{{ $prog->id }}">
                                    {{ $prog->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label>Year Level</x-input-label>
                        <select wire:model.live="year_level"
                            class="w-full border-gray-300 rounded-md dark:bg-gray-900 dark:text-white">
                            <option value="1">Year 1</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                            <option value="4">Year 4</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label>Term (Semester)</x-input-label>
                        <select wire:model.live="term_number"
                            class="w-full border-gray-300 rounded-md dark:bg-gray-900 dark:text-white">
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded text-sm text-gray-600 dark:text-gray-300">
                        Targeting <strong>{{ $studentCount }}</strong> active students.
                    </div>
                </div>
            </div>

            <!-- RIGHT: Select Classes -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold mb-4 dark:text-white">
                    2. Select Schedule Block
                </h3>

                <div class="mb-4">
                    <x-input-label>Semester</x-input-label>
                    <select wire:model.live="semester_id"
                        class="w-full border-gray-300 rounded-md dark:bg-gray-900 dark:text-white">
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}">
                                {{ $sem->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-y-auto max-h-64 border rounded dark:border-gray-700">
                    @if (count($availableClasses) > 0)
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="p-2"><input type="checkbox"></th>
                                    <th class="p-2">Code</th>
                                    <th class="p-2">Section</th>
                                    <th class="p-2">Day/Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($availableClasses as $class)
                                    <tr class="border-t dark:border-gray-700">
                                        <td class="p-2">
                                            <input type="checkbox"
                                                wire:model="selectedClasses"
                                                value="{{ $class->id }}">
                                        </td>
                                        <td class="p-2">{{ $class->course->code }}</td>
                                        <td class="p-2">{{ $class->section_name }}</td>
                                        <td class="p-2">{{ $class->day_of_week }}
                                            {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="p-4 text-gray-500">
                            Select a program to view courses.
                        </p>
                    @endif
                </div>

                @error('selectedClasses')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <!-- Action Button -->
        <div class="flex justify-end">
            <button wire:click="confirmEnrollment"
                {{-- wire:confirm="Are you sure you want to enroll {{ $studentCount }} students into {{ count($selectedClasses) }} classes?" --}}
                class="px-6 py-3 bg-indigo-600 text-white font-bold rounded hover:bg-indigo-700 disabled:opacity-50"
                @if ($studentCount == 0 || count($selectedClasses) == 0) disabled @endif>
                Execute Batch Enrollment
            </button>
        </div>
    </div>
</div>
@push('scripts')
    <x-sweet-alert />
@endpush
