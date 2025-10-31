<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Academic Year') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Academic Year</h3>

                    <form method="POST"
                        action="{{ route('admin.academic-years.update', $academicYear) }}">
                        @csrf
                        @method('PUT')

                        <!-- Year -->
                        <div class="mb-4">
                            <x-input-label for="year"
                                :value="__('Year')" />
                            <x-text-input id="year"
                                class="block mt-1 w-full"
                                type="text"
                                name="year"
                                :value="old('year', $academicYear->year)"
                                required
                                autofocus />
                            <x-input-error :messages="$errors->get('year')"
                                class="mt-2" />
                        </div>

                        <!-- Start Date -->
                        <div class="mb-4">
                            <x-input-label for="start_date"
                                :value="__('Start Date')" />
                            <x-text-input id="start_date"
                                class="block mt-1 w-full"
                                type="date"
                                name="start_date"
                                :value="old('start_date', $academicYear->start_date)"
                                required />
                            <x-input-error :messages="$errors->get('start_date')"
                                class="mt-2" />
                        </div>

                        <!-- End Date -->
                        <div class="mb-4">
                            <x-input-label for="end_date"
                                :value="__('End Date')" />
                            <x-text-input id="end_date"
                                class="block mt-1 w-full"
                                type="date"
                                name="end_date"
                                :value="old('end_date', $academicYear->end_date)"
                                required />
                            <x-input-error :messages="$errors->get('end_date')"
                                class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <x-input-label for="status"
                                :value="__('Status')" />
                            <select id="status"
                                name="status"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                required>
                                <option value="active"
                                    {{ old('status', $academicYear->status) == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive"
                                    {{ old('status', $academicYear->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                            <x-input-error :messages="$errors->get('status')"
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
    </div>
</x-app-layout>
