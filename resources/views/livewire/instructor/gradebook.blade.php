<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ $classSession->course->code }} - {{ $classSession->course->name }}
                </h2>
                <p class="text-gray-500 text-sm">Section {{ $classSession->section_name }}</p>
            </div>
            <a href="{{ route('instructor.dashboard') }}"
                class="text-gray-600 hover:underline">Back to Dashboard</a>
        </div>

        <!-- Student Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Grade</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($classSession->enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $enrollment->student->user->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $enrollment->student->student_id }}
                            </td>
                            <td class="px-6 py-4">
                                <input type="number"
                                    wire:model="grades.{{ $enrollment->id }}"
                                    class="w-24 border-gray-300 dark:bg-gray-900 dark:text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    min="0"
                                    max="100">
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="saveGrade({{ $enrollment->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 font-bold text-sm">
                                    Save
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
    <x-sweet-alert />
@endpush
