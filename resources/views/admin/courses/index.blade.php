<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Courses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Manage Courses</h3>
                        <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add New Course') }}
                        </a>
                    </div>

                    <div class="mb-4">
                        <input type="text" placeholder="Search courses..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </div>

                    @php
                        $headers = [
                            'name' => 'Name',
                            'code' => 'Code',
                            'credits' => 'Credits',
                            'department_name' => 'Department',
                            'instructor_names' => 'Instructors',
                            'semester_name' => 'Semester',
                            'status' => 'Status',
                        ];

                        $data = $courses->map(function ($course) {
                            return [
                                'name' => $course->name,
                                'code' => $course->code,
                                'credits' => $course->credits,
                                'department_name' => $course->department->name ?? 'N/A',
                                'instructor_names' => $course->instructors->pluck('user.name')->join(', ') ?? 'N/A',
                                'semester_name' => $course->semester->name ?? 'N/A',
                                'status' => $course->course_status,
                            ];
                        });

                        $actions = [
                            'show' => [
                                'route' => 'admin.courses.show',
                                'label' => 'View',
                                'params' => ['course' => 'id'],
                                'class' => 'text-blue-600 hover:text-blue-900',
                            ],
                            'edit' => [
                                'route' => 'admin.courses.edit',
                                'label' => 'Edit',
                                'params' => ['course' => 'id'],
                                'class' => 'text-indigo-600 hover:text-indigo-900',
                            ],
                            'delete' => [
                                'route' => 'admin.courses.destroy',
                                'label' => 'Delete',
                                'params' => ['course' => 'id'],
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
                        {{ $courses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
