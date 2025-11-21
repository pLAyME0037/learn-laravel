<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Attendance Record') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Edit Attendance Record</h3>

                <form method="POST"
                    action="{{ route('admin.attendances.update', $attendance) }}">
                    @csrf
                    @method('PUT')

                    <!-- Student -->
                    <div class="mb-4">
                        <x-input-label for="student_id"
                            :value="__('Student')" />
                        <select id="student_id"
                            name="student_id"
                            class="dark:bg-gray-900 bg-gray-200 dark:text-gray-100 text-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                            required>
                            <option value="">Select Student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}"
                                    {{ old('student_id', $attendance->student_id) == $student->id ? 'selected' : '' }}>
                                    {{ $student->user->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('student_id')"
                            class="mt-2" />
                    </div>

                    <!-- Class Schedule -->
                    <div class="mb-4">
                        <x-input-label for="class_schedule_id"
                            :value="__('Class Schedule')" />
                        <select id="class_schedule_id"
                            name="class_schedule_id"
                            class="dark:bg-gray-900 bg-gray-200 dark:text-gray-100 text-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                            required>
                            <option value="">Select Class Schedule</option>
                            @foreach ($classSchedules as $classSchedule)
                                <option value="{{ $classSchedule->id }}"
                                    {{ old('class_schedule_id', $attendance->class_schedule_id) == $classSchedule->id ? 'selected' : '' }}>
                                    {{ $classSchedule->course->name }} - {{ $classSchedule->classroom->name }}
                                    ({{ $classSchedule->start_time }} - {{ $classSchedule->end_time }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('class_schedule_id')"
                            class="mt-2" />
                    </div>

                    <!-- Date -->
                    <div class="mb-4">
                        <x-input-label for="date"
                            :value="__('Date')" />
                        <x-text-input id="date"
                            class="block mt-1 w-full"
                            type="date"
                            name="date"
                            :value="old('date', $attendance->date)"
                            required />
                        <x-input-error :messages="$errors->get('date')"
                            class="mt-2" />
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <x-input-label for="status"
                            :value="__('Status')" />
                        <select id="status"
                            name="status"
                            class="dark:bg-gray-900 bg-gray-200 dark:text-gray-100 text-gray-900 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                            required>
                            <option value="present"
                                {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Present
                            </option>
                            <option value="absent"
                                {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Absent
                            </option>
                            <option value="late"
                                {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>Late</option>
                            <option value="excused"
                                {{ old('status', $attendance->status) == 'excused' ? 'selected' : '' }}>Excused
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('status')"
                            class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.attendances.show', $attendance) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button>
                            {{ __('Update Attendance Record') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
