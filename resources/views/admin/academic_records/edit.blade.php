<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Academic Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Academic Record</h3>

                    <form method="POST"
                        action="{{ route('admin.academic-records.update', $academicRecord) }}">
                        @csrf
                        @method('PUT')

                        <!-- Student -->
                        <div class="mb-4">
                            <x-input-label for="student_id"
                                :value="__('Student')" />
                            <select id="student_id"
                                name="student_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                required>
                                <option value="">Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}"
                                        {{ old('student_id', $academicRecord->student_id) == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('student_id')"
                                class="mt-2" />
                        </div>

                        <!-- Academic Year -->
                        <div class="mb-4">
                            <x-input-label for="academic_year_id"
                                :value="__('Academic Year')" />
                            <select id="academic_year_id"
                                name="academic_year_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                required>
                                <option value="">Select Academic Year</option>
                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}"
                                        {{ old('academic_year_id', $academicRecord->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                        {{ $academicYear->year }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('academic_year_id')"
                                class="mt-2" />
                        </div>

                        <!-- Semester -->
                        <div class="mb-4">
                            <x-input-label for="semester_id"
                                :value="__('Semester')" />
                            <select id="semester_id"
                                name="semester_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                required>
                                <option value="">Select Semester</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}"
                                        {{ old('semester_id', $academicRecord->semester_id) == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('semester_id')"
                                class="mt-2" />
                        </div>

                        <!-- Course -->
                        <div class="mb-4">
                            <x-input-label for="course_id"
                                :value="__('Course')" />
                            <select id="course_id"
                                name="course_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                required>
                                <option value="">Select Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ old('course_id', $academicRecord->course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')"
                                class="mt-2" />
                        </div>

                        <!-- Grade -->
                        <div class="mb-4">
                            <x-input-label for="grade"
                                :value="__('Grade')" />
                            <x-text-input id="grade"
                                class="block mt-1 w-full"
                                type="text"
                                name="grade"
                                :value="old('grade', $academicRecord->grade)"
                                required />
                            <x-input-error :messages="$errors->get('grade')"
                                class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.academic-records.show', $academicRecord) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Academic Record') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
