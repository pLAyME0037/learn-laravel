<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Enrollment') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form method="POST"
                    action="{{ route('admin.enrollments.update', $enrollment->id) }}">
                    @csrf
                    @method('patch')

                    <!-- Student ID -->
                    <div>
                        <x-input-label for="student_id"
                            :value="__('Student')" />
                        <select id="student_id"
                            name="student_id"
                            class="font-mono dark:bg-gray-900 bg-gray-200 dark:text-gray-100 text-gray-900 block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student['id'] }}"
                                    {{ old('student_id', $enrollment->student_id) == $student['id'] ? 'selected' : '' }}>
                                    {{ $student['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('student_id')"
                            class="mt-2" />
                    </div>

                    <!-- Course ID -->
                    <div class="mt-4">
                        <x-input-label for="course_id"
                            :value="__('Course')" />
                        <select id="course_id"
                            name="course_id"
                            class="font-mono dark:bg-gray-900 bg-gray-200 dark:text-gray-100 text-gray-900 block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}"
                                    {{ old('course_id', $enrollment->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }} ({{ $course->code }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('course_id')"
                            class="mt-2" />
                    </div>

                    <!-- Enrollment Date -->
                    <div class="mt-4">
                        <x-input-label for="enrollment_date"
                            :value="__('Enrollment Date')" />
                        <x-text-input id="enrollment_date"
                            class="font-mono dark:bg-gray-900 bg-gray-200 dark:text-gray-100 text-gray-900 block mt-1 w-full"
                            type="date"
                            name="enrollment_date"
                            :value="old('enrollment_date', $enrollment->enrollment_date)"
                            required />
                        <x-input-error :messages="$errors->get('enrollment_date')"
                            class="mt-2" />
                    </div>

                    <!-- Status -->
                    <div class="mt-4">
                        <x-input-label for="status"
                            :value="__('Status')" />
                        <x-text-input id="status"
                            class="font-mono dark:bg-gray-900 bg-gray-200 dark:text-gray-100 text-gray-900 block mt-1 w-full"
                            type="text"
                            name="status"
                            :value="old('status', $enrollment->status)"
                            required />
                        <x-input-error :messages="$errors->get('status')"
                            class="mt-2" />
                    </div>

                    <!-- Academic Year ID -->
                    <div class="mt-4">
                        @php
                            // Define the table layout here
                            $scheduleColumns = [
                                [
                                    'header' => 'Code',
                                    'key' => 'code', // Must match JSON key
                                    'span' => 2, // grid-col-span
                                    'class' => 'font-mono font-bold',
                                ],
                                [
                                    'header' => 'Course Name',
                                    'key' => 'course_name',
                                    'span' => 4,
                                    'class' => 'font-medium text-gray-900 dark:text-gray-200 group-hover:text-white',
                                ],
                                [
                                    'header' => 'Day',
                                    'key' => 'day_of_week',
                                    'span' => 2,
                                ],
                                [
                                    'header' => 'Date/Time',
                                    'key' => 'formatted_time',
                                    'span' => 4,
                                    'align' => 'text-right',
                                    'class' => 'font-mono',
                                ],
                            ];
                        @endphp

                        <x-select-table name="class_schedule_id"
                            label="Class Schedule"
                            :options="$classSchedules"
                            :selected="old('class_schedule_id', $enrollment->class_schedule_id)"
                            :columns="$scheduleColumns" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.enrollments.show', $enrollment->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <x-primary-button class="ms-4">
                            {{ __('Update') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
