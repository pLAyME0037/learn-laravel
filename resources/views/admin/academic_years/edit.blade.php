<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Academic Year') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Edit Academic Year
                </h3>

                <form method="POST"
                    action="{{ route('admin.academic-years.update', $academicYear) }}">
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
                            :value="old('name', $academicYear->name)"
                            required
                            autofocus />
                        <x-input-error :messages="$errors->get('name')"
                            class="mt-2" />
                    </div>

                    <!-- Start Date -->
                    <div class="mb-4">
                        <x-input-label for="start_date"
                            :value="__('Start Date')" />

                        <x-date-picker id="start_date"
                            name="start_date"
                            :value="old('start_date', $academicYear->start_date)" />

                        <x-input-error :messages="$errors->get('start_date')"
                            class="mt-2" />
                    </div>

                    <!-- End Date -->
                    <div class="mb-4">
                        <x-input-label for="end_date"
                            :value="__('End Date')" />

                        <x-date-picker id="end_date"
                            name="end_date"
                            :value="old('end_date', $academicYear->end_date)" />

                        <x-input-error :messages="$errors->get('end_date')"
                            class="mt-2" />
                    </div>

                    <!-- Is Current -->
                    <div class="mb-4">
                        <x-input-label for="is_current"
                            :value="__('Is Current')" />
                        <select id="is_current"
                            name="is_current"
                            class="text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                            required>
                            <option value="1"
                                {{ old('is_current', $academicYear->is_current) == true ? 'selected' : '' }}>
                                Yes
                            </option>
                            <option value="0"
                                {{ old('is_current', $academicYear->is_current) == false ? 'selected' : '' }}>
                                No
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('is_current')"
                            class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.academic-years.show', $academicYear) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button>
                            {{ __('Update Academic Year') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
