<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Students Management') }}
            </h2>
            @can('students.create')
                <a href="{{ route('admin.students.create') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Add New Student') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET"
                        action="{{ route('admin.students.index') }}"
                        class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div>
                                <x-input-label for="search"
                                    :value="__('Search')" />
                                <x-text-input id="search"
                                    name="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('search', $search)"
                                    placeholder="Name, ID, or Email" />
                            </div>

                            <!-- Department Filter -->
                            <div>
                                <x-input-label for="department"
                                    :value="__('Department')" />
                                <select id="department"
                                    name="department"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}"
                                            {{ $department == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Program Filter -->
                            <div>
                                <x-input-label for="program"
                                    :value="__('Program')" />
                                <select id="program"
                                    name="program"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Programs</option>
                                    @foreach ($programs as $prog)
                                        <option value="{{ $prog->id }}"
                                            {{ $program == $prog->id ? 'selected' : '' }}>
                                            {{ $prog->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <x-input-label for="status"
                                    :value="__('Status')" />
                                <select id="status"
                                    name="status"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="active"
                                        {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive"
                                        {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="trashed"
                                        {{ $status === 'trashed' ? 'selected' : '' }}>Trashed</option>
                                </select>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-end space-x-2">
                                <x-primary-button type="submit">
                                    {{ __('Filter') }}
                                </x-primary-button>
                                <a href="{{ route('admin.students.index') }}"
                                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                    {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Students Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($students->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Student ID</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Name</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Department</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Program</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Email</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Admission Date</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($students as $student)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td
                                                class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $student->student_id }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full"
                                                            src="{{ $student->user->profile_picture_url }}"
                                                            alt="{{ $student->user->name }}">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $student->user->name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            @<span>{{ $student->user->username }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->department->name ?? 'N/A' }}
                                            </td>
                                            <td
                                                class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->program->name ?? 'N/A' }}
                                            </td>
                                            <td
                                                class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->user->email }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $student->academic_status === 'active'
                                                        ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                                                        : ($student->academic_status === 'graduated'
                                                            ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
                                                            : ($student->academic_status === 'suspended'
                                                                ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                                                                : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100')) }}">
                                                    {{ ucfirst($student->academic_status) }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $student->admission_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    @can('students.view')
                                                        <a href="{{ route('admin.students.show', $student) }}"
                                                            class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400">View</a>
                                                    @endcan
                                                    @can('students.edit')
                                                        <a href="{{ route('admin.students.edit', $student) }}"
                                                            class="text-green-600 hover:text-green-900 dark:hover:text-green-400">Edit</a>
                                                    @endcan
                                                    @can('students.delete')
                                                        @if ($student->trashed())
                                                            <form action="{{ route('admin.students.restore', $student) }}"
                                                                method="POST"
                                                                class="inline">
                                                                @csrf
                                                                @method('POST')
                                                                <button type="submit"
                                                                    class="text-green-600 hover:text-green-900 dark:hover:text-green-400"
                                                                    onclick="return confirm('Are you sure you want to restore this student?')">
                                                                    Restore
                                                                </button>
                                                            </form>
                                                            <form
                                                                action="{{ route('admin.students.force-delete', $student) }}"
                                                                method="POST"
                                                                class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                                                                    onclick="return confirm('Are you sure you want to permanently delete this student? This action cannot be undone.')">
                                                                    Delete Permanently
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('admin.students.destroy', $student) }}"
                                                                method="POST"
                                                                class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                                                                    onclick="return confirm('Are you sure you want to delete this student?')">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $students->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No students found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $search || $department || $program || $status ? 'Try adjusting your search filters.' : 'Get started by creating a new student.' }}
                            </p>
                            @can('students.create')
                                <div class="mt-6">
                                    <a href="{{ route('admin.students.create') }}"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Add New Student
                                    </a>
                                </div>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
