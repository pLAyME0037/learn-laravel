<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Department Management') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage academic departments and their configurations
                </p>
            </div>
            <a href="{{ route('admin.departments.create') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                <svg class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4" />
                </svg>
                <span>{{ __('Add Department') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Departments</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $departments->total() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Departments</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ $departments->where('is_active', true)->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Faculty</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ $departments->sum('faculty_count') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Programs</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ $departments->sum('active_programs_count') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET"
                        action="{{ route('admin.departments.index') }}"
                        class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <x-input-label for="search"
                                    :value="__('Search Departments')" />
                                <x-text-input id="search"
                                    name="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old('search', $search)"
                                    placeholder="Search by name, code, or description" />
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <x-input-label for="status"
                                    :value="__('Status')" />
                                <select id="status"
                                    name="status"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="active"
                                        {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive"
                                        {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <!-- Sort -->
                            <div>
                                <x-input-label for="sort"
                                    :value="__('Sort By')" />
                                <select id="sort"
                                    name="sort"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="name"
                                        {{ $sort === 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="code"
                                        {{ $sort === 'code' ? 'selected' : '' }}>Code</option>
                                    <option value="faculty_count"
                                        {{ $sort === 'faculty_count' ? 'selected' : '' }}>Faculty Count</option>
                                    <option value="student_count"
                                        {{ $sort === 'student_count' ? 'selected' : '' }}>Student Count</option>
                                    <option value="budget"
                                        {{ $sort === 'budget' ? 'selected' : '' }}>Budget</option>
                                </select>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-end space-x-2">
                                <x-primary-button type="submit">
                                    {{ __('Filter') }}
                                </x-primary-button>
                                <a href="{{ route('admin.departments.index') }}"
                                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                                    {{ __('Reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Departments Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($departments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-700">
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Department</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Head of Department</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Statistics</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Budget</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach ($departments as $department)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <!-- Department Info -->
                                            <td class="px-4 py-4">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                        <span
                                                            class="text-white font-bold text-sm">{{ $department->code }}</span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            <a href="{{ route('admin.departments.show', $department) }}"
                                                                class="hover:text-blue-600 dark:hover:text-blue-400">
                                                                {{ $department->name }}
                                                            </a>
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ Str::limit($department->description, 50) }}
                                                        </div>
                                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                            Founded: {{ $department->founded_year ?: 'N/A' }} •
                                                            Location: {{ $department->office_location ?: 'N/A' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Head of Department -->
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @if ($department->headOfDepartment)
                                                    <div class="flex items-center">
                                                        <img class="h-8 w-8 rounded-full"
                                                            src="{{ $department->headOfDepartment->profile_picture_url }}"
                                                            alt="{{ $department->headOfDepartment->name }}">
                                                        <div class="ml-3">
                                                            <div
                                                                class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $department->headOfDepartment->name }}
                                                            </div>
                                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ $department->headOfDepartment->email }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400 italic">Not
                                                        Assigned</span>
                                                @endif
                                            </td>

                                            <!-- Statistics -->
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white space-y-1">
                                                    <div class="flex justify-between space-x-4">
                                                        <span class="text-gray-500 dark:text-gray-400">Faculty:</span>
                                                        <span
                                                            class="font-medium">{{ $department->faculty_count }}</span>
                                                    </div>
                                                    <div class="flex justify-between space-x-4">
                                                        <span class="text-gray-500 dark:text-gray-400">Students:</span>
                                                        <span
                                                            class="font-medium">{{ $department->student_count }}</span>
                                                    </div>
                                                    <div class="flex justify-between space-x-4">
                                                        <span class="text-gray-500 dark:text-gray-400">Programs:</span>
                                                        <span
                                                            class="font-medium">{{ $department->active_programs_count }}</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Budget -->
                                            <td
                                                class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                {{ $department->formatted_budget }}
                                            </td>

                                            <!-- Status -->
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $department->getStatusBadgeClass() }}">
                                                    {{ $department->getStatusText() }}
                                                </span>
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <!-- View -->
                                                    <a href="{{ route('admin.departments.show', $department) }}"
                                                        class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400"
                                                        title="View Department">
                                                        <svg class="w-4 h-4"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>

                                                    <!-- Edit -->
                                                    <a href="{{ route('admin.departments.edit', $department) }}"
                                                        class="text-green-600 hover:text-green-900 dark:hover:text-green-400"
                                                        title="Edit Department">
                                                        <svg class="w-4 h-4"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>

                                                    <!-- Status Toggle -->
                                                    <form
                                                        action="{{ route('admin.departments.status', $department) }}"
                                                        method="POST"
                                                        class="inline">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit"
                                                            class="{{ $department->is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }} dark:hover:text-yellow-400"
                                                            title="{{ $department->is_active ? 'Deactivate' : 'Activate' }} Department">
                                                            <svg class="w-4 h-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                @if ($department->is_active)
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                                @else
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                                                @endif
                                                            </svg>
                                                        </button>
                                                    </form>

                                                    <!-- Delete -->
                                                    <form
                                                        action="{{ route('admin.departments.destroy', $department) }}"
                                                        method="POST"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                                                            onclick="return confirm('Are you sure you want to delete this department?')"
                                                            title="Delete Department">
                                                            <svg class="w-4 h-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1
