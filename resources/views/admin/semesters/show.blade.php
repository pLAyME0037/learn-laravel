<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Semester Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-900">
                    <div class="mb-4">
                        <x-input-label for="name"
                            :value="__('Name')" />
                        <p>{{ $semester->name }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="start_date"
                            :value="__('Start Date')" />
                        <p>
                            {{ $semester->start_date 
                            ? \Carbon\Carbon::parse($semester->start_date)->format('d-m-Y') 
                            : '' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="end_date"
                            :value="__('End Date')" />
                        <p>
                            {{ $semester->end_date 
                            ? \Carbon\Carbon::parse($semester->end_date)->format('d-m-Y') 
                            : '' }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="academic_year"
                            :value="__('Academic Year')" />
                        <p>{{ $semester->academicYear->name }}</p>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.semesters.edit', $semester) }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.semesters.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
