<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Student Profile') }}
            </h2>
            <div class="flex space-x-2">
                @can('students.edit')
                    <a href="{{ route('admin.students.edit', $student->id) }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors text-sm font-medium">
                        Edit Profile
                    </a>
                @endcan
                <a href="{{ route('admin.students.index') }}"
                    class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors text-sm font-medium">
                    Back to List
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
                    <x-profile-image size="md"
                        src="{{ $student->user->profile_picture_url }}"
                        alt="{{ $student->user->username }}" />
                </div>

                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $student->user->name }}
                    </h3>
                    <p class="text-indigo-600 dark:text-indigo-400 font-medium font-mono">
                        {{ $student->student_id }}
                    </p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-700">
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                                Program
                            </span>
                            <div class="font-medium text-gray-900 dark:text-gray-200 mt-1">
                                {{ $student->program->name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-700">
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                                Department
                            </span>
                            <div class="font-medium text-gray-900 dark:text-gray-200 mt-1">
                                {{ $student->program->major->department->name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border dark:border-gray-700">
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold">
                                Status
                            </span>
                            <div class="mt-1">
                                <span
                                    class="px-2 py-1 text-xs font-bold rounded-full
                                    {{ $student->academic_status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                                    {{ ucfirst($student->academic_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT COLUMN -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- 2. Academic Information -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Academic Progress</h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 gap-y-4 gap-x-8">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Current Level
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">
                                    Year {{ ceil($student->current_term / 2) }},
                                    Semester {{ $student->current_term % 2 == 0 ? 2 : 1 }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Term
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">
                                    Term {{ $student->current_term }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    CGPA
                                </label>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">
                                    {{ $student->cgpa }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Financial Status
                                </label>
                                <p
                                    class="text-sm mt-1 {{ $student->has_outstanding_balance ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $student->has_outstanding_balance ? 'Outstanding Balance' : 'Clear' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Personal Information -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Personal Details
                            </h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 gap-y-4 gap-x-8">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Date of Birth
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">
                                    {{ $student->attributes['dob'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Gender
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">
                                    {{ \App\Models\Dictionary::label('gender', $student->attributes['gender'] ?? '') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Nationality
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">
                                    {{ $student->attributes['nationality'] ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Email
                                </label>
                                <p class="text-sm text-gray-900 dark:text-white mt-1">
                                    {{ $student->user->email }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Enrollments Table (If any) -->
                    @if ($student->enrollments->count() > 0)
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    Current Enrollments
                                </h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead
                                        class="bg-gray-50 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-300 uppercase">
                                        <tr>
                                            <th class="px-6 py-3">Course</th>
                                            <th class="px-6 py-3">Section</th>
                                            <th class="px-6 py-3">Status</th>
                                            <th class="px-6 py-3">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach ($student->enrollments as $enrollment)
                                            <tr>
                                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                                    {{ $enrollment->classSession->course->code }} -
                                                    {{ $enrollment->classSession->course->name }}
                                                </td>
                                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                                    {{ $enrollment->classSession->section_name }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="px-2 py-1 text-xs font-bold rounded bg-blue-50 text-blue-700">
                                                        {{ ucfirst($enrollment->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                                    {{ $enrollment->final_grade ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                                    Phone
                                </label>
                                <p class="text-sm font-medium dark:text-white">
                                    {{ $student->contactDetail->phone ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400 uppercase">
                                    Emergency
                                    Contact</label>
                                <p class="text-sm font-medium dark:text-white mt-1">
                                    {{ $student->contactDetail->emergency_name ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $student->contactDetail->emergency_phone ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Address Info -->
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h4
                            class="text-lg font-medium text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                            Address</h4>
                        @if ($student->address)
                            <div class="text-sm text-gray-900 dark:text-white">
                                <p class="font-medium">{{ $student->address->current_address }}</p>
                                @if ($student->address->village)
                                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                                        {{ $student->address->village->name_kh }} <br>
                                        {{ $student->address->village->commune->name_kh }},
                                        {{ $student->address->village->commune->district->name_kh }} <br>
                                        {{ $student->address->village->commune->district->province->name_kh }}
                                    </p>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">
                                No address recorded.
                            </p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 gap-2 border dark:border-gray-700 items-center">
                        <x-secondary-button href="{{ route('admin.students.index') }}"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 dark:hover:bg-red-900/40 rounded justify-center">
                            {{ __('Back') }}
                        </x-secondary-button>
                        <x-secondary-button href="{{ route('admin.students.edit', $student->id) }}"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 dark:hover:bg-indigo-900/40 rounded justify-center">
                            {{ __('Edit') }}
                        </x-secondary-button>

                        <h4 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-3">
                            Admin Actions
                        </h4>
                        <div class="space-y-2">

                                <form action="#"
                                    method="POST"
                                    onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-secondary-button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded">
                                        Delete Record (not implement)
                                    </x-secondary-button>
                                </form>

                            <x-secondary-button class="w-full text-left px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded">
                                Print ID Card (not implement)
                            </x-secondary-button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
