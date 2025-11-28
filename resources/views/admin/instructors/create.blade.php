<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Instructor') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <form method="POST"
                    action="{{ route('admin.instructors.store') }}">
                    @csrf

                    <!-- User ID -->
                    <div>
                        <x-input-label for="user_id"
                            :value="__('User')" />
                        <select id="user_id"
                            name="user_id"
                            class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')"
                            class="mt-2" />
                    </div>

                    <!-- Faculty ID -->
                    <div class="mt-4">
                        <x-input-label for="faculty_id"
                            :value="__('Faculty')" />
                        <select id="faculty_id"
                            name="faculty_id"
                            class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Faculty</option>
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}"
                                    {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('faculty_id')"
                            class="mt-2" />
                    </div>

                    <!-- Department ID -->
                    <div class="mt-4">
                        <x-input-label for="department_id"
                            :value="__('Department')" />
                        <select id="department_id"
                            name="department_id"
                            class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('department_id')"
                            class="mt-2" />
                    </div>

                    <!-- Payscale -->
                    <div class="mt-4">
                        <x-input-label for="payscale"
                            :value="__('Payscale')" />
                        <x-text-input id="payscale"
                            class="block mt-1 w-full"
                            type="text"
                            name="payscale"
                            :value="old('payscale')"
                            required />
                        <x-input-error :messages="$errors->get('payscale')"
                            class="mt-2" />
                    </div>

                    <!-- info -->
                    <div class="mt-4">
                        <x-input-label for="info"
                            :value="__('Infomation')" />
                        <x-text-input id="info"
                            class="block mt-1 w-full"
                            type="text"
                            name="info"
                            :value="old('info')" />
                        <x-input-error :messages="$errors->get('info')"
                            class="mt-2" />
                    </div>
</x-app-layout>
