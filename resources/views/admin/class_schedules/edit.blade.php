<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Class Schedule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Class Schedule</h3>

                    <form method="POST" action="{{ route('admin.class-schedules.update', $classSchedule) }}">
                        @csrf
                        @method('PUT')

                        <!-- Course -->
                        <div class="mb-4">
                            <x-input-label for="course_id" :value="__('Course')" />
                            <select id="course_id" name="course_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required>
                                <option value="">Select Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $classSchedule->course_id) == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                        </div>

                        <!-- Classroom -->
                        <div class="mb-4">
                            <x-input-label for="classroom_id" :value="__('Classroom')" />
                            <select id="classroom_id" name="classroom_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required>
                                <option value="">Select Classroom</option>
                                @foreach ($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}" {{ old('classroom_id', $classSchedule->classroom_id) == $classroom->id ? 'selected' : '' }}>{{ $classroom->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('classroom_id')" class="mt-2" />
                        </div>

                        <!-- Instructor -->
                        <div class="mb-4">
                            <x-input-label for="instructor_id" :value="__('Instructor')" />
                            <select id="instructor_id" name="instructor_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required>
                                <option value="">Select Instructor</option>
                                @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ old('instructor_id', $classSchedule->instructor_id) == $instructor->id ? 'selected' : '' }}>{{ $instructor->user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('instructor_id')" class="mt-2" />
                        </div>

                        <!-- Day of Week -->
                        <div class="mb-4">
                            <x-input-label for="day_of_week" :value="__('Day of Week')" />
                            <select id="day_of_week" name="day_of_week" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required>
                                <option value="">Select Day</option>
                                <option value="Monday" {{ old('day_of_week', $classSchedule->day_of_week) == 'Monday' ? 'selected' : '' }}>Monday</option>
                                <option value="Tuesday" {{ old('day_of_week', $classSchedule->day_of_week) == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                                <option value="Wednesday" {{ old('day_of_week', $classSchedule->day_of_week) == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                                <option value="Thursday" {{ old('day_of_week', $classSchedule->day_of_week) == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                                <option value="Friday" {{ old('day_of_week', $classSchedule->day_of_week) == 'Friday' ? 'selected' : '' }}>Friday</option>
                                <option value="Saturday" {{ old('day_of_week', $classSchedule->day_of_week) == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                                <option value="Sunday" {{ old('day_of_week', $classSchedule->day_of_week) == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                            </select>
                            <x-input-error :messages="$errors->get('day_of_week')" class="mt-2" />
                        </div>

                        <!-- Start Time -->
                        <div class="mb-4">
                            <x-input-label for="start_time" :value="__('Start Time')" />
                            <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" :value="old('start_time', $classSchedule->start_time)" required />
                            <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                        </div>

                        <!-- End Time -->
                        <div class="mb-4">
                            <x-input-label for="end_time" :value="__('End Time')" />
                            <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" :value="old('end_time', $classSchedule->end_time)" required />
                            <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.class-schedules.show', $classSchedule) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Class Schedule') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
