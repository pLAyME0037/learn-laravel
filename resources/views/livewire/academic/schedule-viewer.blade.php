<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                Weekly Timetable
            </h2>
            <a href="{{ route('academic.dashboard') }}"
                class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Back to Dashboard
            </a>
        </div>
        @php
            $days = [
                'Mon' => 'Monday',
                'Tue' => 'Tuesday',
                'Wed' => 'Wednesday',
                'Thu' => 'Thursday',
                'Fri' => 'Friday',
                // 'Sat' => 'Saturday',
                // 'Sun' => 'Sunday',
            ];
        @endphp
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div
                class="grid grid-cols-1 md:grid-cols-5 divide-y md:divide-y-0 md:divide-x divide-gray-200 dark:divide-gray-700">

                @foreach ($days as $shortDay => $fullDay)
                    <div class="min-h-[500px] bg-gray-50 dark:bg-gray-900/50 flex flex-col">

                        <!-- Day Header -->
                        <div
                            class="p-4 text-center font-bold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                            {{ $fullDay }}
                            @if ($shortDay === \Carbon\Carbon::now()->format('D'))
                                <span class="ml-2 text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">
                                    Today
                                </span>
                            @endif
                        </div>

                        <!-- Classes Column -->
                        <div class="p-2 flex-1 space-y-3">
                            @if (isset($schedule[$shortDay])) 
                            {{-- Use the SHORT key ($shortDay) to lookup data --}}
                                @foreach ($schedule[$shortDay] as $class)
                                    <div
                                        class="bg-white dark:bg-gray-800 p-3 rounded-lg shadow border-l-4 border-indigo-500 hover:shadow-md transition-shadow group">

                                        <!-- Time -->
                                        <div
                                            class="flex justify-between items-center text-xs font-mono text-gray-500 dark:text-gray-400 mb-2">
                                            <span>{{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}</span>
                                            <span>-</span>
                                            <span>{{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}</span>
                                        </div>

                                        <!-- Course Info -->
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                                            {{ $class->course->code }}
                                        </h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">
                                            {{ $class->course->name }}
                                        </p>

                                        <!-- Details -->
                                        <div
                                            class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-xs text-gray-500">
                                            <span class="bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">
                                                Sec {{ $class->section_name }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-3 h-3 mr-1"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $class->classroom->room_number ?? 'TBA' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="h-full flex items-center justify-center">
                                    <span class="text-gray-300 dark:text-gray-600 text-sm italic">
                                        Free Day
                                    </span>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
