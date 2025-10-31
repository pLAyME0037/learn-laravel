<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Academic Records') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Manage Academic Records</h3>
                        <a href="{{ route('admin.academic-records.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add New Academic Record') }}
                        </a>
                    </div>

                    <div class="mb-4">
                        <input type="text"
                            placeholder="Search academic records..."
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>

                    @php
                        $headers = [
                            'student_name' => 'Student Name',
                            'course_name' => 'Course Name',
                            'semester_name' => 'Semester',
                            'grade' => 'Grade',
                            'credits_earned' => 'Credits Earned',
                            'status' => 'Status',
                        ];

                        $data = $academicRecords->map(function ($academicRecord) {
                            return [
                                'student_name' => $academicRecord->student->user->name,
                                'course_name' => $academicRecord->course->name,
                                'semester_name' => $academicRecord->semester->name,
                                'grade' => $academicRecord->grade,
                                'credits_earned' => $academicRecord->credits_earned,
                                'status' => $academicRecord->grade_status,
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.academic_records.show',
                                'label' => 'View',
                                'params' => ['academic_record' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.academic_records.edit',
                                'label' => 'Edit',
                                'params' => ['academic_record' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.academic_records.destroy',
                                'label' => 'Delete',
                                'params' => ['academic_record' => 'id'],
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

                    <div class="mt-4">
                        {{ $academicRecords->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
