<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Academic Year: ') }}{{ $academicYear->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-700 dark:text-gray-200">
                    <strong class="pr-0">Start Year:</strong> {{ $academicYear->start_date }}
                    <strong class="pl-4">End Year:</strong> {{ $academicYear->end_date }}
                </p>

                <hr class="my-4">

                <h2 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-200">
                    Semesters
                </h2>
                @forelse($academicYear->semesters as $semester)
                    <div class="card mb-4 border rounded-lg shadow-sm dark:border-gray-700">
                        <div
                            class="card-header bg-gray-100 dark:bg-gray-700 p-4 rounded-t-lg flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200">
                                {{ $semester->name }} ({{ $semester->start_date->format('M d, Y') }} -
                                {{ $semester->end_date->format('M d, Y') }})
                            </h3>
                            <span
                                class="badge bg-{{ $semester->is_current ? 'success' : 'secondary' }} text-gray-700 dark:text-gray-200 px-2 py-1 rounded">
                                {{ $semester->is_current ? 'Current' : 'Past' }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <h4 class="text-md font-semibold mb-2 text-gray-700 dark:text-gray-200">
                                Courses Offered
                            </h4>
                            @forelse($semester->courses->groupBy('department.name') as $departmentName => $courses)
                                <h5 class="text-md font-medium mt-3 text-gray-700 dark:text-gray-200">
                                    {{ $departmentName }}
                                </h5>
                                <ul class="list-disc pl-5">
                                    @foreach ($courses as $course)
                                        <li class="text-gray-700 dark:text-gray-200">
                                            {{ $course->name }} ({{ $course->code }})
                                            <ul class="list-circle pl-5">
                                                @forelse($course->classSchedules as $classSchedule)
                                                    <li class="text-gray-700 dark:text-gray-200">
                                                        Class: {{ $classSchedule->day_of_week }}
                                                        {{ $classSchedule->start_time }}-{{ $classSchedule->end_time }}
                                                        (Enrolled: {{ $classSchedule->enrollments_count }} students)
                                                    </li>
                                                @empty
                                                    <li class="text-gray-700 dark:text-gray-200">
                                                        No classes scheduled.
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                                @empty
                                    <p class="text-gray-700 dark:text-gray-200">
                                        No courses offered in this semester.
                                    </p>
                                @endforelse
                            </div>
                        </div>
                        @empty
                            <p class="text-gray-700 dark:text-gray-200">
                                No semesters found for this academic year.
                            </p>
                        @endforelse

                        <div class="mt-4">
                            <a href="{{ route('admin.academic_years.index') }}"
                                class="btn btn-secondary bg-gray-500 hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold py-2 px-4 rounded">
                                Back to Academic Years
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </x-app-layout>
