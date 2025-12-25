<div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

    <!-- Header Controls -->
    <div
        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ $classSession->course->name }}
            </h2>
            <p class="text-sm text-gray-500">
                {{ $classSession->course->code }} - Sec {{ $classSession->section_name }}
            </p>
        </div>

        <div class="flex items-center gap-4">
            <select wire:model.live="selectedDate"
                class="bg-white dark:bg-gray-900 border-gray-300 rounded-md">
                @foreach ($sessionDates as $date => $label)
                    <option value="{{ $date }}"
                        class="{{ \Carbon\Carbon::parse($date)->isFuture() ? 'text-gray-400 italic' : '' }}">
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <!-- Export/Import Buttons -->
            <div class="flex gap-2">
                <button wire:click="exportTemplate"
                    class="text-sm bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">
                    Download Excel
                </button>

                <div class="relative overflow-hidden inline-block">
                    <button class="text-sm bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
                        Upload Excel
                    </button>
                    <input type="file"
                        wire:model="importFile"
                        wire:change="import"
                        class="absolute top-0 left-0 opacity-0 cursor-pointer w-full h-full">
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-between items-center">

            <!-- Mark All Buttons -->
            <div class="space-x-2">
                <span class="text-xs text-gray-500 uppercase font-bold mr-2">
                    Mark All:
                </span>
                <button wire:click="markAll('present')"
                    class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                    Present
                </button>
                <button wire:click="markAll('absent')"
                    class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">
                    Absent
                </button>
            </div>

            <button wire:click="save"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow font-bold transition-colors">
                Save Attendance
            </button>
        </div>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($classSession->enrollments as $enrollment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                        "
                        >
                        <td class="px-6 py-4 w-1/3">
                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $enrollment->student->user->name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $enrollment->student->student_id }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-6"
                                wire:key="row-{{ $enrollment->id }}-{{ $selectedDate }}"
                                x-data="{ val: @entangle('attendance.' . $enrollment->id) }">
                                @foreach ($statusOptions as $key => $label)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="radio"
                                            name="attendance_{{ $enrollment->id }}"
                                            x-model="val"
                                            value="{{ $key }}"
                                            wire:model="attendance.{{ $enrollment->id }}"
                                            class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                        <span
                                            class="ml-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
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
