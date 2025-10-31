<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Course Prerequisite') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Course Prerequisite</h3>

                    <form method="POST" action="{{ route('admin.course-prerequisites.update', $coursePrerequisite) }}">
                        @csrf
                        @method('PUT')

                        <!-- Course -->
                        <div class="mb-4">
                            <x-input-label for="course_id" :value="__('Course')" />
                            <select id="course_id" name="course_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required>
                                <option value="">Select Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('course_id', $coursePrerequisite->course_id) == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                        </div>

                        <!-- Prerequisite Course -->
                        <div class="mb-4">
                            <x-input-label for="prerequisite_course_id" :value="__('Prerequisite Course')" />
                            <select id="prerequisite_course_id" name="prerequisite_course_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required>
                                <option value="">Select Prerequisite Course</option>
                                @foreach ($prerequisiteCourses as $prerequisiteCourse)
                                    <option value="{{ $prerequisiteCourse->id }}" {{ old('prerequisite_course_id', $coursePrerequisite->prerequisite_course_id) == $prerequisiteCourse->id ? 'selected' : '' }}>{{ $prerequisiteCourse->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('prerequisite_course_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.course-prerequisites.show', $coursePrerequisite) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Course Prerequisite') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
