<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Course') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Course</h3>

                    <form method="POST"
                        action="{{ route('admin.courses.update', $course) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name"
                                :value="__('Name')" />
                            <x-text-input id="name"
                                class="block mt-1 w-full"
                                type="text"
                                name="name"
                                :value="old('name', $course->name)"
                                required
                                autofocus />
                            <x-input-error :messages="$errors->get('name')"
                                class="mt-2" />
                        </div>

                        <!-- Code -->
                        <div class="mb-4">
                            <x-input-label for="code"
                                :value="__('Code')" />
                            <x-text-input id="code"
                                class="block mt-1 w-full"
                                type="text"
                                name="code"
                                :value="old('code', $course->code)"
                                required />
                            <x-input-error :messages="$errors->get('code')"
                                class="mt-2" />
                        </div>

                        <!-- Credits -->
                        <div class="mb-4">
                            <x-input-label for="credits"
                                :value="__('Credits')" />
                            <x-text-input id="credits"
                                class="block mt-1 w-full"
                                type="number"
                                name="credits"
                                :value="old('credits', $course->credits)"
                                required />
                            <x-input-error :messages="$errors->get('credits')"
                                class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description"
                                :value="__('Description')" />
                            <x-textarea-input id="description"
                                class="block mt-1 w-full"
                                name="description">{{ old('description', $course->description) }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')"
                                class="mt-2" />
                        </div>

                        <!-- Department -->
                        <div class="mb-4">
                            <x-input-label for="department_id"
                                :value="__('Department')" />
                            <select id="department_id"
                                name="department_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_id', $course->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('department_id')"
                                class="mt-2" />
                        </div>

                        <!-- Semester -->
                        <div class="mb-4">
                            <x-input-label for="semester_id"
                                :value="__('Semester')" />
                            <select id="semester_id"
                                name="semester_id"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                                <option value="">Select Semester</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->id }}"
                                        {{ old('semester_id', $course->semester_id) == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('semester_id')"
                                class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.courses.show', $course) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Course') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
