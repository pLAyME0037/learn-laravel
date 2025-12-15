<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Instructor Profile') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.instructors.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">
                    Back
                </a>
                <a href="{{ route('admin.instructors.edit', $instructor->id) }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Identity Card -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 flex items-start space-x-6">
                <div
                    class="h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center text-blue-500 text-3xl font-bold">
                    {{ substr($instructor->user->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $instructor->user->name }}
                    </h3>
                    <p class="text-blue-600 dark:text-blue-400 font-medium">
                        {{ $instructor->staff_id }}
                    </p>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Department:
                        <span class="font-semibold text-gray-700 dark:text-gray-300">
                            {{ $instructor->department->name ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contact & Address (Reused Layout) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h4
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                        Contact</h4>
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-sm font-medium dark:text-gray-200">
                                {{ $instructor->user->email }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Phone</dt>
                            <dd class="text-sm font-medium dark:text-gray-200">
                                {{ $instructor->contactDetail->phone ?? 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h4
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                        Address
                    </h4>
                    @if ($instructor->address)
                        <p class="text-sm dark:text-gray-200">
                            {{ $instructor->address->current_address }}
                        </p>
                        @if ($instructor->address->village)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $instructor->address->village->name_en }},
                                {{ $instructor->address->village->commune->name_en }},
                                {{ $instructor->address->village->commune->district->name_en }}
                            </p>
                        @endif
                    @else
                        <span class="text-gray-500 italic text-sm">
                            No address found.
                        </span>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
