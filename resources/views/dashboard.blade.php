<x-app-layout title="Dashboard"
    :pageTitle="__('Dashboard')"
    :sidebarCollapsed="session('sidebar_collapsed', false)">

    <!-- Welcome Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            Welcome back, {{ auth()->user()->name }}!
        </h2>
        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
            Here's what's happening today in your workspace.
        </p>
    </div>

    <!-- Role-Based Content -->
    @if (auth()->user()->hasRole('student'))
        {{-- Student View: Quick Academic Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-academic.stat-card title="CGPA"
                value="{{ auth()->user()->student->cgpa ?? '0.00' }}"
                icon="academic-cap"
                color="indigo" />
            <x-academic.stat-card title="Current Term"
                value="{{ auth()->user()->student->academic_progress ?? 'N/A' }}"
                icon="book-open"
                color="emerald" />
            <x-academic.stat-card title="Status"
                value="{{ ucfirst(auth()->user()->student->academic_status ?? 'Unknown') }}"
                icon="user"
                color="blue" />
        </div>

        <div class="grid grid-cols-1 gap-6">
            {{-- Quick Links to Modules --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-800 dark:text-white mb-4">
                    Quick Access
                </h3>
                <div class="flex gap-4">
                    <a href="{{ route('academic.dashboard') }}"
                        class="flex-1 text-center p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition">
                        <span class="text-indigo-600 dark:text-indigo-400 font-bold">
                            Go to Academic Portal
                        </span>
                    </a>
                    <a href="{{ route('academic.schedule') }}"
                        class="flex-1 text-center p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                        <span class="text-blue-600 dark:text-blue-400 font-bold">
                            View Schedule
                        </span>
                    </a>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasAnyRole(['Super Administrator', 'admin']))
        {{-- Admin View: System Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-stats-card title="Total Students"
                value="{{ \App\Models\Student::count() }}"
                icon="users"
                color="blue" />
            <x-stats-card title="Total Staff"
                value="{{ \App\Models\Instructor::count() }}"
                icon="briefcase"
                color="purple" />
            <x-stats-card title="Active Courses"
                value="{{ \App\Models\Course::count() }}"
                icon="book-open"
                color="green" />
            <x-stats-card title="Pending Issues"
                value="0"
                icon="exclamation-circle"
                color="red" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Reusing your component but renaming it to be generic --}}
            <x-recent-activity title="System Logs" />

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.students.create') }}"
                        class="block w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition">
                        + Register New Student
                    </a>
                    <a href="{{ route('admin.academic.batch-enroll') }}"
                        class="block w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-lg transition">
                        + Run Batch Enrollment
                    </a>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('staff'))
        {{-- Instructor View --}}
        <div class="p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800">
            <h3 class="text-lg font-bold text-indigo-900 dark:text-indigo-100">
                Instructor Workspace
            </h3>
            <p class="text-sm text-indigo-700 dark:text-indigo-300 mt-1">
                Manage your classes and grades from the specialized portal.
            </p>
            <a href="{{ route('instructor.dashboard') }}"
                class="inline-block mt-4 px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                Open Instructor Portal
            </a>
        </div>
    @else
        {{-- Fallback / Guest View --}}
        <div class="text-center py-12">
            <p class="text-gray-500">
                Your account does not have a specific role assigned. Please contact IT.
            </p>
        </div>
    @endif

</x-app-layout>
