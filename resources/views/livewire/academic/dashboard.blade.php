<div class="py-12">
    @if (!$student)
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            No Student Profile found linked to this account. (Are you logged in as Admin?)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- === LEFT COLUMN (Main Board & Widgets) === -->
                <div class="lg:col-span-3 space-y-6">

                    <!-- 1. Big Board (Hero Section) -->
                    <x-academic.hero-section :student="$student" />

                    <!-- 2. Stats Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <x-academic.stat-card title="CGPA"
                            value="{{ $student->cgpa ?? '0.00' }}"
                            icon="academic-cap"
                            color="indigo" />

                        <x-academic.stat-card title="Credits Earned"
                            value="{{ $student->total_credits_earned ?? 0 }}"
                            icon="check-badge"
                            color="emerald" />

                        <x-academic.stat-card title="Academic Status"
                            value="{{ ucfirst($student->academic_status ?? 'N/A') }}"
                            icon="user"
                            color="{{ $student->academic_status === 'active' ? 'blue' : 'red' }}" />
                    </div>

                    <!-- 3. Today's Schedule -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Today's Schedule ({{ \Carbon\Carbon::now()->format('l') }})
                        </h3>

                        @if (count($todaysClasses) > 0)
                            <div class="space-y-3">
                                @foreach ($todaysClasses as $enrollment)
                                    <div
                                        class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex-shrink-0 w-16 text-center">
                                            <span class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                                                {{ \Carbon\Carbon::parse($enrollment->classSession->start_time)->format('H:i') }}
                                            </span>
                                            <span class="block text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($enrollment->classSession->end_time)->format('H:i') }}
                                            </span>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $enrollment->classSession->course->code }} -
                                                {{ $enrollment->classSession->course->name }}
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Room: {{ $enrollment->classSession->classroom->room_number ?? 'TBA' }} •
                                                Instructor:
                                                {{ $enrollment->classSession->instructor->name ?? 'Staff' }}
                                            </p>
                                        </div>
                                        <div>
                                            <span
                                                class="px-2 py-1 text-xs font-bold rounded bg-indigo-100 text-indigo-700">
                                                {{ $enrollment->classSession->section_name }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                                No classes scheduled for today.
                            </div>
                        @endif
                    </div>

                    <!-- 4. Bottom Section: Charts & Progress -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Progress Chart -->
                        <x-academic.semester-progress :percent="$semesterProgress" />

                        <!-- Activity Feed -->
                        <x-academic.activity-feed :activities="$activities" />
                    </div>

                </div>

                <!-- === RIGHT COLUMN (Sidebar Links) === -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Profile / Quick User Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 text-center">
                        <div class="">
                            {{-- {{ substr($student->user->name, 0, 2) }} --}}
                            <x-profile-image size="md"
                                src="{{ $student->user?->profile_picture_url }}"
                                alt="{{ $student->user?->username }}" />
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $student->user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $student->student_id }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $student->program->name ?? '' }}</p>
                    </div>

                    <!-- Links Widget -->
                    <x-academic.sidebar-links :links="$quickLinks" />
                </div>

            </div>
        </div>
    @endif
</div>
