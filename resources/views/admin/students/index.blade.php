<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Students') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('admin.students.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Add New Student') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @php
                        $headers = [
                            'id' => 'ID',
                            'user_id' => 'User Name',
                            'student_id' => 'Student ID',
                            'date_of_birth' => 'Date of Birth',
                            'gender_id' => 'Gender',
                            'address' => 'Address',
                            'phone_number' => 'Phone Number',
                            'enrollment_date' => 'Enrollment Date',
                            'program_id' => 'Program',
                            'major_id' => 'Major',
                            'academic_year_id' => 'Academic Year',
                            'created_at' => 'Created At',
                            'updated_at' => 'Updated At',
                        ];

                        $data = $students->map(function ($student) {
                            return [
                                'id' => $student->id,
                                'user_id' => $student->user->name,
                                'student_id' => $student->student_id,
                                'date_of_birth' => $student->date_of_birth
                                    ? \Carbon\Carbon::parse($student->date_of_birth)->format('d-m-Y')
                                    : '',
                                'gender_id' => $student->gender->name,
                                'address' => $student->address,
                                'phone_number' => $student->phone_number,
                                'enrollment_date' => $student->enrollment_date
                                    ? \Carbon\Carbon::parse($student->enrollment_date)->format('d-m-Y')
                                    : '',
                                'program_id' => $student->program->name,
                                'major_id' => $student->major->name,
                                'academic_year_id' => $student->academicYear->year,
                                'created_at' => $student->created_at
                                    ? \Carbon\Carbon::parse($student->created_at)->format('d-m-Y H:i:s')
                                    : '',
                                'updated_at' => $student->updated_at
                                    ? \Carbon\Carbon::parse($student->updated_at)->format('d-m-Y H:i:s')
                                    : '',
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.students.show',
                                'label' => 'View',
                                'params' => ['student'],
                            ],
                            'edit' => [
                                'route' => 'admin.students.edit',
                                'label' => 'Edit',
                                'params' => ['student'],
                            ],
                            'delete' => [
                                'route' => 'admin.students.destroy',
                                'label' => 'Delete',
                                'params' => ['student'],
                                'confirm' => 'Are you sure you want to delete this student?',
                            ],
                        ];
                    @endphp

                    <x-dynamic-table :headers="$headers"
                        :data="$data"
                        :actions="$actions" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
