<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Department Management') }}
            </h2>
            @can('departments.create')
                <a href="{{ route('admin.departments.create') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Add Department') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.departments.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <x-input-label for="search" :value="__('Search')" />
                                <x-text-input id="search" name="search" type="text" 
                                              class="mt-1 block w-full" 
                                              :value="old('search', $search)" 
                                              placeholder="Search departments..." />
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="trashed" {{ $status === 'trashed' ? 'selected' : '' }}>Trashed</option>
                                    <option value="">All Status</option>
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

            <!-- Departments Grid -->
            @if($departments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($departments as $department)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <!-- Department Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $department->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $department->code }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $department->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                        {{ $department->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <!-- Department Info -->
                                <div class="space-y-2 mb-4">
                                    @if($department->hod)
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            HOD: {{ $department->hod->name }}
                                        </div>
                                    @endif

                                    @if($department->email)
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $department->email }}
                                        </div>
                                    @endif

                                    @if($department->office_location)
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $department->office_location }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Statistics -->
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $department->users_count }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Staff</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $department->students_count }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Students</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $department->programs_count }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Programs</div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-between items-center pt-4 border-t dark:border-gray-700">
                                    <div class="flex space-x-2">
                                        @can('departments.view')
                                            <a href="{{ route('admin.departments.show', $department) }}" 
                                               class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400 text-sm">
                                                View
                                            </a>
                                        @endcan
                                        @can('departments.edit')
                                            <a href="{{ route('admin.departments.edit', $department) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400 text-sm">
                                                Edit
                                            </a>
                                        @endcan
                                    </div>
                                    @can('departments.delete')
                                        @if($department->trashed())
                                            <form action="{{ route('admin.departments.restore', $department->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900 dark:hover:text-green-400 text-sm"
                                                        onclick="return confirm('Restore this department?')">
                                                    Restore
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 dark:hover:text-red-400 text-sm"
                                                        onclick="return confirm('Delete this department?')">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $departments->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No departments found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ $search || $status ? 'Try adjusting your search filters.' : 'Get started by creating your first department.' }}
                        </p>
                        @can('departments.create')
                            <div class="mt-6">
                                <a href="{{ route('admin.departments.create') }}" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Add Department
                                </a>
                            </div>
                        @endcan
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>