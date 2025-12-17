    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Section 1: Key Metrics (Stats) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-dashboard.stat-card title="Total Students"
                value="{{ $stats['total_students'] }}"
                icon="users"
                color="indigo"
                trend="+{{ $stats['new_this_month'] }} this month" />

            <x-dashboard.stat-card title="Active Departments"
                value="{{ $stats['departments'] }}"
                icon="building-library"
                color="emerald" />

            <x-dashboard.stat-card title="Faculty & Staff"
                value="{{ $stats['total_staff'] }}"
                icon="briefcase"
                color="blue" />

            <x-dashboard.stat-card title="System Status"
                value="Operational"
                icon="server"
                color="green"
                :is-text="true" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Section 2: Main Content (Recent Students Table) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Recent Registrations
                        </h3>
                        <a href="{{ route('admin.students.index') }}"
                            class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                            View All
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Department</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentStudents as $student)
                                    <tr
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <x-profile-image size="xs"
                                                    src="{{ $student->user->profile_picture_url }}"
                                                    alt="{{ $student->user->username }}" />
                                                <span>{{ $student->user->name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $student->department->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $student->academic_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($student->academic_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $student->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Section 3: Sidebar (Quick Actions & Notices) -->
            <div class="space-y-6">
                <!-- Quick Actions Widget -->
                <x-dashboard.quick-actions />

                <!-- Notices Widget -->
                <x-dashboard.notices :notices="$notices" />
            </div>

        </div>
    </div>
