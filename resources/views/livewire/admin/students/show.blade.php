<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Student Profile') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.students.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    Back
                </a>
                <a href="{{ route('admin.students.edit', $student->id) }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Edit Profile
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 1. Header Card (Identity) -->
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 flex items-start space-x-6">
                <!-- Profile Pic -->
                <div class="flex-shrink-0">
                    @if ($student->user->profile_pic)
                        <img src="{{ Storage::url($student->user->profile_pic) }}"
                            class="h-24 w-24 rounded-full object-cover border-4 border-indigo-50">
                    @else
                        <div
                            class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 text-3xl font-bold">
                            {{ substr($student->user->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $student->user->name }}</h3>
                    <p class="text-indigo-600 dark:text-indigo-400 font-medium">{{ $student->student_id }}</p>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Program</span>
                            <div class="font-semibold dark:text-gray-200">{{ $student->program->name ?? 'N/A' }}</div>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Status</span>
                            <div>
                                <span
                                    class="px-2 py-1 text-xs font-bold rounded {{ $student->academic_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($student->academic_status) }}
                                </span>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Current Term</span>
                            <div class="font-semibold dark:text-gray-200">
                                Year {{ ceil($student->current_term / 2) }}, Sem
                                {{ $student->current_term % 2 == 0 ? 2 : 1 }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- 2. Personal Info & Contacts -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h4
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                        Personal Details</h4>

                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-sm font-medium dark:text-gray-200">{{ $student->user->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="text-sm font-medium dark:text-gray-200">
                                {{ $student->contactDetail->phone ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Date of Birth</dt>
                            <dd class="text-sm font-medium dark:text-gray-200">
                                {{ $student->attributes['dob'] ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Gender</dt>
                            <dd class="text-sm font-medium dark:text-gray-200">
                                {{ \App\Models\Dictionary::label('gender', $student->attributes['gender'] ?? '') }}
                            </dd>
                        </div>

                        <div class="pt-4 border-t dark:border-gray-700">
                            <dt class="text-sm text-gray-500 dark:text-gray-400 mb-1">Emergency Contact</dt>
                            <dd class="text-sm dark:text-gray-200">
                                <strong>{{ $student->contactDetail->emergency_name ?? 'N/A' }}</strong><br>
                                {{ $student->contactDetail->emergency_phone ?? '' }}
                                <span
                                    class="text-gray-400 text-xs">({{ $student->contactDetail->emergency_relation ?? '' }})</span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- 3. Address Information -->
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h4
                        class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                        Address</h4>

                    @if ($student->address)
                        <div class="space-y-3">
                            <div>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">Street / House</span>
                                <span
                                    class="block text-sm dark:text-gray-200">{{ $student->address->current_address }}</span>
                            </div>

                            @if ($student->address->village)
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Village</span>
                                        <span
                                            class="block text-sm dark:text-gray-200">{{ $student->address->village->name_en }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Commune</span>
                                        <span
                                            class="block text-sm dark:text-gray-200">{{ $student->address->village->commune->name_en }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">District</span>
                                        <span
                                            class="block text-sm dark:text-gray-200">{{ $student->address->village->commune->district->name_en }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Province</span>
                                        <span
                                            class="block text-sm dark:text-gray-200">{{ $student->address->village->commune->district->province->name_en }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">No address recorded.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
