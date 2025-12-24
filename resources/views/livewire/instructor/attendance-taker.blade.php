<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ $classSession->course->name }}
            </h2>
            <p class="text-sm text-gray-500">
                Attendance for: {{ \Carbon\Carbon::parse($date)->format('D, M d, Y') }}
            </p>
        </div>
        <div>
            <input type="date"
                wire:model.live="date"
                class="border-gray-300 dark:bg-gray-900 rounded-md">
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300">
                Student List
            </span>
            <button wire:click="save"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow text-sm font-bold">
                Save Attendance
            </button>
        </div>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($classSession->enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $enrollment->student->user->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $enrollment->student->student_id }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-4">
                                @foreach ($statusOptions as $key => $label)
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio"
                                            name="attendance_{{ $enrollment->id }}"
                                            value="{{ $key }}"
                                            wire:model="attendance.{{ $enrollment->id }}"
                                            class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@push('scripts')
    <x-sweet-alert />
@endpush
