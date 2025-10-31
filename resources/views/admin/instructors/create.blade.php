<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Instructor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST"
                        action="{{ route('admin.instructors.store') }}">
                        @csrf

                        <!-- User ID -->
                        <div>
                            <x-input-label for="user_id"
                                :value="__('User')" />
                            <select id="user_id"
                                name="user_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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

                        <!-- Department ID -->
                        <div class="mt-4">
                            <x-input-label for="department_id"
                                :value="__('Department')" />
                            <select id="department_id"
                                name="department_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('department_id')"
                                class="mt-2" />
                        </div>

                        <!-- Hire Date -->
                        <div class="mt-4">
                            <x-input-label for="hire_date"
                                :value="__('Hire Date')" />
                            <x-text-input id="hire_date"
                                class="block mt-1 w-full"
                                type="date"
                                name="hire_date"
                                :value="old('hire_date')"
                                required />
                            <x-input-error :messages="$errors->get('hire_date')"
                                class="mt-2" />
                        </div>

                        <!-- Rank -->
                        <div class="mt-4">
                            <x-input-label for="rank"
                                :value="__('Rank')" />
                            <x-text-input id="rank"
                                class="block mt-1 w-full"
                                type="text"
                                name="rank"
                                :value="old('rank')"
                                required />
                            <x-input-error :messages="$errors->get('rank')"
                                class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.instructors.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Create') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
