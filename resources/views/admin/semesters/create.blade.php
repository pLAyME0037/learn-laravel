<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Semester') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST"
                        action="{{ route('admin.semesters.store') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name"
                                :value="__('Name')" />
                            <x-text-input id="name"
                                class="block mt-1 w-full"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus />
                            <x-input-error :messages="$errors->get('name')"
                                class="mt-2" />
                        </div>

                        <!-- Start Date -->
                        <div class="mt-4">
                            <x-input-label for="start_date"
                                :value="__('Start Date')" />
                            <x-text-input id="start_date"
                                class="block mt-1 w-full"
                                type="date"
                                name="start_date"
                                :value="old('start_date')"
                                required />
                            <x-input-error :messages="$errors->get('start_date')"
                                class="mt-2" />
                        </div>

                        <!-- End Date -->
                        <div class="mt-4">
                            <x-input-label for="end_date"
                                :value="__('End Date')" />
                            <x-text-input id="end_date"
                                class="block mt-1 w-full"
                                type="date"
                                name="end_date"
                                :value="old('end_date')"
                                required />
                            <x-input-error :messages="$errors->get('end_date')"
                                class="mt-2" />
                        </div>

                        <!-- Academic Year -->
                        <div class="mt-4">
                            <x-input-label for="academic_year_id"
                                :value="__('Academic Year')" />
                            <x-select-input id="academic_year_id"
                                class="block mt-1 w-full"
                                name="academic_year_id"
                                :value="old('academic_year_id')"
                                required>
                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}">{{ $academicYear->year }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('academic_year_id')"
                                class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
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
