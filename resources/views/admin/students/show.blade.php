<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Student Details') }}
            </h2>
            <div class="flex space-x-2">
                @can('students.edit')
                    <a href="{{ route('admin.students.edit', $student) }}"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('Edit Student') }}
                    </a>
                @endcan
                <a href="{{ route('admin.students.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Back to Students') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Student Header -->
                <div class="flex items-start space-x-6 mb-8">
                    <img class="h-24 w-24 rounded-full"
                        src="{{ $student->user->profile_picture_url }}"
                        alt="{{ $student->user->name }}">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $student->user->name }}
                        </h1>
                        <p class="text-lg text-gray-600 dark:text-gray-400">
                            {{ $student->student_id }}
                        </p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span
                                class="px-3 py-1 text-sm font-semibold rounded-full 
                                    {{ $student->academic_status === 'active'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                                        : ($student->academic_status === 'graduated'
                                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
                                            : ($student->academic_status === 'suspended'
                                                ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100')) }}">
                                {{ ucfirst($student->academic_status) }}
                            </span>
                            <span
                                class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                {{ ucfirst($student->enrollment_status) }}
                            </span>
                            <span
                                class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100">
                                {{ $student->fee_category }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Academic Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Academic Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Department
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->department->name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Program
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->program->name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Admission Date
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->admission_date->format('M d, Y') }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Current Semester
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->current_semester }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Expected Graduation
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->expected_graduation?->format('M d, Y') ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Year Level
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->year_level_name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Has
                                        Outstanding Balance
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->has_outstanding_balance ? 'Yes' : 'No' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Personal Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Date of Birth
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->date_of_birth->format('M d, Y') }}
                                        ({{ $student->date_of_birth->age }} years old)</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Gender
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ ucfirst($student->gender->name) }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Nationality
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->nationality }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Blood Group
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->blood_group ?? 'N/A' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Previous Education
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->previous_education ?? 'N/A' }}</p>
                                </div>
                                @if ($student->has_disability)
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Disability Details
                                        </label>
                                        <p class="text-sm text-gray-900 dark:text-white">
                                            {{ $student->disability_details ?? 'N/A' }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Address Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Current Address
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $student->current_address }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                        Permanent Address</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $student->permanent_address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- User Information -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                User Information
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Username
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        @<span>{{ $student->user->username }}</span>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Email
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->user->email }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Phone
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->phone }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Account Status
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->user->is_active ? 'Active' : 'Inactive' }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Email Verified
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->user->email_verified_at ? $student->user->email_verified_at->format('M d, Y') : 'Not Verified' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Emergency Contact
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Contact Name
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->emergency_contact_name }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Contact Phone
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->emergency_contact_phone }}
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        Relationship
                                    </label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $student->emergency_contact_relation }}

                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Quick Actions
                            </h3>
                            <div class="space-y-2">
                                <a href="{{ route('admin.students.index') }}"
                                    class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors text-center block">
                                    Back
                                </a>
                                @can('edit.students')
                                    <a href="{{ route('admin.students.edit', $student) }}"
                                        class="w-full bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-lg transition-colors text-center block">
                                        Edit Student
                                    </a>
                                @endcan
                                @can('delete.students')
                                    @if ($student->trashed())
                                        <form action="{{ route('admin.students.restore', $student) }}"
                                            method="POST"
                                            class="w-full">
                                            @csrf
                                            @method('POST')
                                            <button type="submit"
                                                class="w-full bg-green-300 hover:bg-green-400 text-white px-4 py-2 rounded-lg transition-colors"
                                                onclick="return confirm('Are you sure you want to restore this student?')">
                                                Restore Student
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.students.force-delete', $student) }}"
                                            method="POST"
                                            class="w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full bg-red-300 hover:bg-red-400 text-white px-4 py-2 rounded-lg transition-colors"
                                                onclick="return confirm('Are you sure you want to permanently delete this student? This action cannot be undone.')">
                                                Delete Permanently
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.students.destroy', $student) }}"
                                            method="POST"
                                            class="w-full">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors"
                                                onclick="return confirm('Are you sure you want to delete this student?')">
                                                Delete Student
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Sections (Enrollments, Academic Records) -->
                @if ($student->enrollments->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Enrollments
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <div class="overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-100 dark:bg-gray-600">
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Course</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Semester</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Status</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Enrollment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-500">
                                        @foreach ($student->enrollments as $enrollment)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                                    {{ $enrollment->course->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $enrollment->semester->name }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ ucfirst($enrollment->status) }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $enrollment->enrollment_date->format('M d, Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($student->academicRecords->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Academic Records
                        </h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <div class="overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-100 dark:bg-gray-600">
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Semester</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                GPA</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Credits</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-500">
                                        @foreach ($student->academicRecords as $record)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                                    {{ $record->semester }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $record->gpa }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $record->credits_earned }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ ucfirst($record->status) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
