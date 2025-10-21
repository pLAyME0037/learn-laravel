<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Academic Year') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST"
                        action="{{ route('admin.academic_years.update', $academicYear->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name"
                                :value="__('Name')" class="dark:text-gray-200" />
                            <x-text-input id="name"
                                class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700"
                                type="text"
                                name="name"
                                :value="old('name', $academicYear->name)"
                                required
                                autofocus />
                            <x-input-error :messages="$errors->get('name')"
                                class="mt-2" />
                        </div>

                        <!-- Start Date -->
                        <div class="mt-4">
                            <x-input-label for="start_date"
                                :value="__('Start Date')" class="dark:text-gray-200" />
                            <x-text-input id="start_date"
                                class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700"
                                type="date"
                                name="start_date"
                                :value="old('start_date', $academicYear->start_date)"
                                required />
                            <x-input-error :messages="$errors->get('start_date')"
                                class="mt-2" />
                        </div>

                        <!-- End Date -->
                        <div class="mt-4">
                            <x-input-label for="end_date"
                                :value="__('End Date')" class="dark:text-gray-200" />
                            <x-text-input id="end_date"
                                class="block mt-1 w-full dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700"
                                type="date"
                                name="end_date"
                                :value="old('end_date', $academicYear->end_date)"
                                required />
                            <x-input-error :messages="$errors->get('end_date')"
                                class="mt-2" />
                        </div>

                        <!-- Is Current -->
                        <div class="mt-4">
                            <label for="is_current"
                                class="inline-flex items-center">
                                <!-- Hidden field to ensure 0 is sent when unchecked -->
                                <input type="hidden" name="is_current" value="0"> 
                                <input id="is_current"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-700 dark:checked:bg-indigo-600"
                                    name="is_current"
                                    value="1"
                                    {{ old('is_current', $academicYear->is_current) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Set as Current Academic Year') }}</span>
                            </label>
                            <x-input-error :messages="$errors->get('is_current')"
                                class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
