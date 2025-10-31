<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Enrollment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}"
                                        {{ old('student_id', $enrollment->student_id) == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->name }}
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
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
                                class="block mt-1 w-full"
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
                                class="block mt-1 w-full"
                                type="text"
                                name="status"
                                :value="old('status', $enrollment->status)"
                                required />
                            <x-input-error :messages="$errors->get('status')"
                                class="mt-2" />
                        </div>

                        <!-- Academic Year ID -->
                        <div class="mt-4">
                            <x-input-label for="academic_year_id"
                                :value="__('Academic Year')" />
                            <select id="academic_year_id"
                                name="academic_year_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Academic Year</option>
                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}"
                                        {{ old('academic_year_id', $enrollment->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                        {{ $academicYear->year }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('academic_year_id')"
                                class="mt-2" />
                        </div>

                        <!-- Semester ID -->
                        <div class="mt-4">
                            <x-input-label for="semester_id"
                                :value="__('Semester')" />
                            <select id="semester_id"
                                name="semester_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Semester</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}"
                                        {{ old('semester_id', $enrollment->semester_id) == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('semester_id')"
                                class="mt-2" />
                        </div>

                        <!-- Class Schedule ID -->
                        <div class="mt-4">
                            <x-input-label for="class_schedule_id"
                                :value="__('Class Schedule')" />
                            <select id="class_schedule_id"
                                name="class_schedule_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Class Schedule</option>
                                @foreach ($classSchedules as $classSchedule)
                                    <option value="{{ $classSchedule->id }}"
                                        {{ old('class_schedule_id', $enrollment->class_schedule_id) == $classSchedule->id ? 'selected' : '' }}>
                                        {{ $classSchedule->course->name }} - {{ $classSchedule->day_of_week }}
                                        {{ $classSchedule->start_time }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('class_schedule_id')"
                                class="mt-2" />
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
    </div>
</x-app-layout>
