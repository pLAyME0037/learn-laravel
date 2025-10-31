<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Academic Record Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Academic Record Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label :value="__('Student Name')" />
                            <p class="mt-1 text-sm text-gray-600">{{ $academicRecord->student->user->name }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Academic Year')" />
                            <p class="mt-1 text-sm text-gray-600">{{ $academicRecord->academicYear->year }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Semester')" />
                            <p class="mt-1 text-sm text-gray-600">{{ $academicRecord->semester->name }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Course')" />
                            <p class="mt-1 text-sm text-gray-600">{{ $academicRecord->course->name }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Grade')" />
                            <p class="mt-1 text-sm text-gray-600">{{ $academicRecord->grade }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Created At')" />
                            <p class="mt-1 text-sm text-gray-600">{{ $academicRecord->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Updated At')" />
                            <p class="mt-1 text-sm text-gray-600">{{ $academicRecord->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.academic-records.edit', $academicRecord) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.academic-records.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
