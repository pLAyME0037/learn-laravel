<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

    <!-- STEP 1: CONTEXT HEADER -->
    <div
        class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-indigo-500 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                Batch Enrollment Wizard
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Select an Academic Period to begin.
            </p>
        </div>
        <div class="w-72">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                Academic Period
            </label>
            <select wire:model.live="semester_id"
                class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md shadow-sm">
                @foreach ($semesters as $sem)
                    <option value="{{ $sem['id'] }}">{{ $sem['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- LEFT COLUMN: SELECTION LOGIC (Span 4) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Filter Panel -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                    1. Target Group
                </h3>

                <div class="space-y-4">
                    <!-- Department -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                        <select wire:model.live="department_id"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Program (With Counts) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Program</label>
                        <select wire:model.live="program_id"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md">
                            <option value="">Select Program</option>
                            @foreach ($programs as $prog)
                                @php $count = $programCounts[$prog->id] ?? 0; @endphp
                                <option value="{{ $prog->id }}">{{ $prog->name }} ({{ $count }} students)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Cohort Panel -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">
                    2. Batch Block
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Year Level
                        </label>
                        <select wire:model.live="year_level"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md">
                            @foreach ([1, 2, 3, 4] as $y)
                                @php
                                    $t1 = ($y - 1) * 2 + 1;
                                    $t2 = ($y - 1) * 2 + 2;
                                    $count = ($cohortCounts[$t1] ?? 0) + ($cohortCounts[$t2] ?? 0);
                                @endphp
                                <option value="{{ $y }}">
                                    Year {{ $y }} ({{ $count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Term
                        </label>
                        <select wire:model.live="term_number"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md">
                            @foreach ([1, 2] as $t)
                                @php
                                    $targetTerm = ($year_level - 1) * 2 + $t;
                                    $count = $cohortCounts[$targetTerm] ?? 0;
                                @endphp
                                <option value="{{ $t }}">
                                    Semester {{ $t }} ({{ $count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Live Count Indicator -->
                <div
                    class="mt-6 p-4 rounded-lg flex justify-between items-center {{ $targetStudentCount > 0 ? 'bg-blue-50 border border-blue-200 dark:bg-blue-900/30 dark:border-blue-800' : 'bg-gray-100 dark:bg-gray-700' }}">
                    <div>
                        <span class="block text-xs uppercase font-bold text-gray-500 dark:text-gray-400">
                            Target Students
                        </span>
                        @if ($targetStudentCount > 0)
                            <span class="text-xs text-blue-600 dark:text-blue-400">
                                Ready to enroll
                            </span>
                        @else
                            <span class="text-xs text-red-500">
                                No active students
                            </span>
                        @endif
                    </div>
                    <div class="text-3xl font-bold text-gray-800 dark:text-white">
                        {{ $targetStudentCount }}
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: RESULTS (Span 8) -->
        <div class="lg:col-span-8 space-y-6">

            {{-- MISSING COURSES WARNING --}}
            @if (count($missingCourses) > 0)
                <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400"
                                viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Scheduling Gap Detected</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>The Roadmap requires these courses, but they are <strong>not scheduled</strong> for
                                    this semester:</p>
                                <ul class="list-disc list-inside mt-1">
                                    @foreach ($missingCourses as $missing)
                                        <li>{{ $missing->code }} - {{ $missing->name }}</li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('admin.academic.schedule') }}"
                                    class="block mt-2 font-bold underline">Go to Schedule Manager to fix this</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm min-h-[500px]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-900 dark:text-white text-lg flex items-center">
                        <span
                            class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm mr-3">3</span>
                        Recommended Schedule
                    </h3>
                    @if (count($availableClasses) > 0)
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
                            {{ count($availableClasses) }} Classes Found
                        </span>
                    @endif
                </div>

                @if (count($availableClasses) > 0)
                    <!-- Schedule Card Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($availableClasses as $class)
                            <label
                                class="relative flex items-start p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors {{ in_array($class->id, $selectedClasses) ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                <div class="flex items-center h-5">
                                    <input type="checkbox"
                                        wire:model.live="selectedClasses"
                                        value="{{ $class->id }}"
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex justify-between">
                                        <span class="block text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $class->course->code }}
                                        </span>
                                        <span
                                            class="text-xs font-mono text-gray-500 bg-white dark:bg-gray-800 border rounded px-1.5 py-0.5">
                                            Sec {{ $class->section_name }}
                                        </span>
                                    </div>
                                    <span
                                        class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $class->course->name }}</span>

                                    <div class="mt-3 flex items-center justify-between text-xs">
                                        <div class="flex items-center text-indigo-600 dark:text-indigo-400 font-medium">
                                            <svg class="w-3 h-3 mr-1"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $class->day_of_week }}
                                            {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}
                                        </div>
                                        <div class="text-gray-500">
                                            {{ $class->instructor->name ?? 'Staff' }}
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Execute Button Area -->
                    <div class="mt-8 pt-6 border-t dark:border-gray-700 flex justify-end items-center gap-4">
                        <button wire:click="confirmRollback"
                            class="text-red-500 hover:text-red-700 text-sm font-bold flex items-center transition-colors px-3 py-2 rounded hover:bg-red-50 dark:hover:bg-red-900/20"
                            title="Delete all enrollments for this cohort in this semester">
                            <svg class="w-4 h-4 mr-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Reset Cohort Schedule
                        </button>
                        <div class="text-right text-sm text-gray-500">
                            <p>Enroll <span class="font-bold text-gray-900 dark:text-white">
                                    {{ $targetStudentCount }}
                                </span>
                                students
                            </p>
                            <p>Into <span class="font-bold text-gray-900 dark:text-white">
                                    {{ count($selectedClasses) }}
                                </span>
                                classes
                            </p>
                        </div>
                        <button wire:click="analyzeEnrollment"
                            @if ($targetStudentCount == 0 || count($selectedClasses) == 0) disabled @endif
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 transform transition hover:scale-105">
                            <svg class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Enroll Cohort
                        </button>
                    </div>
                @elseif($program_id)
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center h-full text-center p-8">
                        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-4">
                            <svg class="w-10 h-10 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">No Classes Scheduled</h3>
                        <p class="text-sm text-gray-500 mt-2 max-w-sm">
                            We couldn't find any class sessions for <strong>Year {{ $year_level }}, Sem
                                {{ $term_number }}</strong> of this program in the selected semester.
                        </p>
                        <div class="mt-6 flex gap-3">
                            <a href="{{ route('admin.academic.schedule') }}"
                                class="text-indigo-600 hover:underline text-sm">
                                Check Schedule
                            </a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ $program ? route('admin.programs.curriculum', $program->id) : '#' }}"
                                class="text-indigo-600 hover:underline text-sm">
                                Check Roadmap
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Initial State -->
                    <div class="flex flex-col items-center justify-center h-full text-center opacity-50">
                        <svg class="w-16 h-16 text-gray-300 mb-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <p class="text-gray-500">
                            Select a Program and Cohort on the left to begin.
                        </p>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Analysis Modal -->
    @if ($showAnalysisModal && $analysis)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500 bg-opacity-75">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
                <h3 class="text-xl font-bold mb-4 dark:text-white">
                    Pre-Flight Check
                </h3>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-green-50 p-4 rounded border border-green-200">
                        <span class="block text-2xl font-bold text-green-700">{{ $analysis['to_create'] }}</span>
                        <span class="text-xs text-green-600">
                            New Enrollments
                        </span>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded border border-yellow-200">
                        <span class="block text-2xl font-bold text-yellow-700">{{ $analysis['duplicates'] }}</span>
                        <span class="text-xs text-yellow-600">
                            Already Enrolled (Skipped)
                        </span>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showAnalysisModal', false)"
                        class="px-4 py-2 text-gray-500">
                        Cancel
                    </button>
                    <button wire:click="runEnrollment"
                        class="px-4 py-2 bg-indigo-600 text-white rounded font-bold hover:bg-indigo-700">
                        Confirm & Execute
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
@push('scripts')
    <x-sweet-alert />
@endpush
