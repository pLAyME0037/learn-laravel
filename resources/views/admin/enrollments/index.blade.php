<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Enrollments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Manage Enrollments
                        </h3>
                        <a href="{{ route('admin.enrollments.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Create New Enrollment
                        </a>
                    </div>

                    @php
                        $headers = [
                            'student_name' => 'Student Name',
                            'course_name' => 'Course Name',
                            'class_schedule_details' => 'Class Schedule',
                            'enrollment_date' => 'Enrollment Date',
                            'status' => 'Status',
                        ];

                        $data = $enrollments->map(function ($enrollment) {
                            return [
                                'id' => $enrollment->id,
                                'student_name' => $enrollment->student->user?->name ?? 'N/A',
                                'course_name' => $enrollment->course->name,
                                'class_schedule_details' => $enrollment->classSchedule
                                    ? (
                                        ($enrollment->classSchedule->schedule_date?->format('Y-m-d') ?? 'N/A')
                                        . ' ('
                                        . ($enrollment->classSchedule->start_time?->format('H:i') ?? 'N/A')
                                        . '-'
                                        . ($enrollment->classSchedule->end_time?->format('H:i') ?? 'N/A')
                                        . ')'
                                    )
                                    : 'N/A',
                                'enrollment_date' => $enrollment->formatted_enrollment_date,
                                'status' => $enrollment->enrollment_status,
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.enrollments.show',
                                'label' => 'View',
                                'params' => ['enrollment' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.enrollments.edit',
                                'label' => 'Edit',
                                'params' => ['enrollment' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.enrollments.destroy',
                                'label' => 'Delete',
                                'params' => ['enrollment' => 'id'],
                                'class' => 'text-red-600 hover:text-red-900',
                                'method' => 'DELETE',
                            ],
                        ];
                    @endphp
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <x-dynamic-table :headers="$headers"
                        :data="$data"
                        :actions="$actions"
                        :options="['wrapperClass' => 'border border-gray-200']" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
