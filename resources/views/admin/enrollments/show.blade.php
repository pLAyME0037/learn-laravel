<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Enrollment Details') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Enrollment Details #{{ $enrollment->id }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Student')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $enrollment->student->user->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Course')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $enrollment->course->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Enrollment Date')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $enrollment->enrollment_date->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Status')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $enrollment->status }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Academic Year')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $enrollment->academicYear->year ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Semester')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $enrollment->semester->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Class')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $enrollment->classSchedule->course->name ?? 'N/A' }} -
                            {{ $enrollment->classSchedule->day_of_week ?? 'N/A' }}
                            {{ $enrollment->classSchedule->start_time ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Created')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $enrollment->created_at }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Updated At')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $enrollment->updated_at }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                        {{ __('Edit') }}
                    </a>
                    <a href="{{ route('admin.enrollments.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
