<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Instructor Profile') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.instructors.edit', $instructor->id) }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Edit Profile
                </a>
                <a href="{{ route('admin.instructors.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 1. Header Card (Identity) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 flex items-start space-x-6">
                <!-- Profile Pic -->
                <div class="flex-shrink-0">
                    <x-profile-image size="lg"
                        src="{{ $instructor->user->profile_picture_url }}"
                        alt="{{ $instructor->user->name }}" />
                </div>

                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $instructor->user->name }}
                    </h3>
                    <p class="text-indigo-600 dark:text-indigo-400 font-medium font-mono">
                        {{ $instructor->staff_id }}
                    </p>

                    <div class="mt-4 flex gap-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-700">
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                                Department
                            </span>
                            <div class="font-medium text-gray-900 dark:text-gray-200 mt-1">
                                {{ $instructor->department->name ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-700">
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                                Specialization
                            </span>
                            <div class="font-medium text-gray-900 dark:text-gray-200 mt-1">
                                {{ $instructor->attributes['specialization'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Address Info -->
            <div class="lg:col-span-1 bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                    Address
                </h4>
                @if ($instructor->address)
                    <div class="text-sm text-gray-900 dark:text-white">
                        <p class="font-medium">{{ $instructor->address->current_address }}</p>
                        @if ($instructor->address->village)
                            <p class="mt-2 text-gray-600 dark:text-gray-400">
                                {{ $instructor->address->village->name_kh }} <br>
                                {{ $instructor->address->village->commune->name_kh }},
                                {{ $instructor->address->village->commune->district->name_kh }} <br>
                                {{ $instructor->address->village->commune->district->province->name_kh }}
                            </p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic">No address recorded.</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT COLUMN -->
            <div class="lg:col-span-2 space-y-6">

                <!-- 3. Teaching Schedule (Fix: Use classSessions relation) -->
                {{-- {{ dd($instructor->classSessions->toArray()) }} --}}
                @if ($instructor->classSessions->count() > 0)
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Teaching Schedule
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead
                                    class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-300 uppercase">
                                    <tr>
                                        <th class="px-6 py-3">Course</th>
                                        <th class="px-6 py-3">Section</th>
                                        <th class="px-6 py-3">Room</th>
                                        <th class="px-6 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($instructor->classSessions as $session)
                                        <tr class="hover:dark:bg-gray-700 hover:bg-slate-200">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white ">
                                                {{ $session->course->code }} - {{ $session->course->name }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                                {{ $session->section_name }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                                {{ $session->classroom->room_number ?? 'TBA' }}
                                                -
                                                {{ $session->day_of_week }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                                {{ $session->status }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow text-center text-gray-500">
                        No active classes assigned.
                    </div>
                @endif

            </div>

            <!-- RIGHT COLUMN -->
            <div class="space-y-6">

                <!-- 5. Contact Info -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h4
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                        Contact
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Email</label>
                            <p class="text-sm font-medium dark:text-white">{{ $instructor->user->email }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">Phone</label>
                            <p class="text-sm font-medium dark:text-white">
                                {{ $instructor->contactDetail->phone ?? 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                                Emergency Contact
                            </label>
                            <p class="text-sm font-medium dark:text-white mt-1">
                                {{ $instructor->contactDetail->emergency_name ?? 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $instructor->contactDetail->emergency_phone ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 6. Quick Actions -->
                <div class="grid grid-cols-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 gap-2 border dark:border-gray-700 items-center">
                    <x-secondary-button href="{{ route('admin.instructors.index') }}"
                        class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 dark:hover:bg-red-900/40 rounded justify-center">
                        {{ __('Back') }}
                    </x-secondary-button>
                    <x-secondary-button href="{{ route('admin.instructors.edit', $instructor->id) }}"
                        class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 dark:hover:bg-indigo-900/40 rounded justify-center">
                        {{ __('Edit') }}
                    </x-secondary-button>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
