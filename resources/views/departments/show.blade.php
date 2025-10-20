<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Department Details') }}
            </h2>
            <div class="flex space-x-2">
                @can('departments.edit')
                    <a href="{{ route('admin.departments.edit', $department) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                        {{ __('Edit Department') }}
                    </a>
                @endcan
                <a href="{{ route('admin.departments.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                    {{ __('Back to Departments') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-9">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Department Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="bg-indigo-100 dark:bg-indigo-900 p-3 rounded-lg">
                                <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $department->name }}</h1>
                                <p class="text-lg text-gray-600 dark:text-gray-400">{{ $department->code }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $department->is_active ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                        {{ $department->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Established: {{ $department->established_year ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Department Details Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left Column - Basic Information -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Description -->
                            @if($department->description)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Description</h3>
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <p class="text-gray-700 dark:text-gray-300">{{ $department->description }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Contact Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Contact Information</h3>
                                <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg divide-y divide-gray-200 dark:divide-gray-600">
                                    @if($department->email)
                                        <div class="p-4 flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Email</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $department->email }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($department->phone)
                                        <div class="p-4 flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Phone</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $department->phone }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($department->office_location)
                                        <div class="p-4 flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Office Location</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $department->office_location }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Head of Department -->
                            @if($department->hod)
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Head of Department</h3>
                                    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                        <div class="flex items-center space-x-3">
                                            <img class="h-12 w-12 rounded-full" src="{{ $department->hod->profile_picture_url }}" alt="{{ $department->hod->name }}">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $department->hod->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $department->hod->email }}</p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">Head of Department</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right Column - Statistics & Quick Actions -->
                        <div class="space-y-6">
                            <!-- Statistics -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Department Statistics</h3>
                                <div class="space-y-3">
                                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Total Staff</p>
                                                <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $department->staff_count }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-green-800 dark:text-green-300">Total Students</p>
                                                <p class="text-2xl font-semibold text-green-600 dark:text-green-400">{{ $department->student_count }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-purple-800 dark:text-purple-300">Total Programs</p>
                                                <p class="text-2xl font-semibold text-purple-600 dark:text-purple-400">{{ $department->program_count }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($department->budget)
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10a2 2 0 11-4 0 2 2 0 014 0zM6 18a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Annual Budget</p>
                                                    <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400">${{ number_format($department->budget, 2) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Quick Actions</h3>
                                <div class="space-y-2">
                                    @can('students.view')
                                        <a href="{{ route('admin.students.index', ['department' => $department->id]) }}" 
                                           class="w-full flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">View Students</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    
                                    @can('users.view')
                                        <a href="#" 
                                           class="w-full flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">View Staff</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    
                                    @can('programs.manage')
                                        <a href="#" 
                                           class="w-full flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">View Programs</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timestamps -->
                    <div class="mt-8 pt-6 border-t dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-500 dark:text-gray-400">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">Created:</span>
                                {{ $department->created_at->format('F j, Y g:i A') }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">Last Updated:</span>
                                {{ $department->updated_at->format('F j, Y g:i A') }}
                            </div>
                            @if($department->trashed())
                                <div>
                                    <span class="font-medium text-red-600 dark:text-red-400">Deleted:</span>
                                    {{ $department->deleted_at->format('F j, Y g:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>