<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    @if (
        !auth()->user()
            ?->hasAnyRole(['admin', 'Super Administrator', 'staff']))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Icon -->
                    <svg class="h-5 w-5 text-yellow-400"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        You do not have permission to access this URL.
                    </p>
                </div>
            </div>
        </div>
    @else
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
            My Classes - {{ $activeSemesterName }}
        </h2>

        @if ($myClasses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($myClasses as $class)
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span
                                        class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">
                                        {{ $class->course->code }}
                                    </span>
                                    <h3 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $class->course->name }}
                                    </h3>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Section {{ $class->section_name }}
                                    </span>
                                </div>
                                <div
                                    class="bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-3 py-1 rounded-full text-xs font-bold">
                                    {{ $class->enrolled_count }} Students
                                </div>
                            </div>

                            <div class="mt-6 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $class->day_of_week }} {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('instructor.gradebook', $class->id) }}"
                                    class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                    Open Gradebook
                                </a>
                            </div>
                            <div class="mt-6">
                                <a href="{{ route('instructor.attendance', $class->id) }}"
                                    class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                    Attendence
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                <p class="text-gray-500 dark:text-gray-400">You have no classes assigned for this semester.</p>
            </div>
        @endif
    @endif
</div>
