{{-- Custom CSS to hide scrollbar but keep functionality --}}
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div
    class="h-full flex flex-col justify-between bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700">

    {{-- 2. USER PROFILE (Compact) --}}
    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 shrink-0">
        <div class="flex items-center p-3"
            :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
            <div class="relative shrink-0">
                <img src="{{ Auth::user()->profile_picture_url ?? asset('images/default-avatar.png') }}"
                    class="w-8 h-8 rounded-full object-cover ring-2 ring-white dark:ring-gray-700"
                    alt="Avatar">
                <span
                    class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full">
                </span>
            </div>

            <div x-show="!sidebarCollapsed"
                class="overflow-hidden transition-opacity duration-200">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ Auth::user()->email }}
                </p>
            </div>
        </div>
    </div>

    @php
        $menuItems = [
            // === MAIN SECTION ===
            [
                'type' => 'heading',
                'label' => 'Main',
            ],
            [
                'type' => 'link',
                'label' => 'Dashboard',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
                'route' => 'dashboard',
                'can' => null, // Accessible by all authenticated users
            ],

            // === ADMIN SECTION ===
            [
                'type' => 'heading',
                'label' => 'Administration',
                'can' => ['view.users', 'view.roles'], // Only visible to admins/staff
            ],

            [
                'type' => 'link',
                'label' => 'Dashboard',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
                'route' => 'admin.dashboard',
                'can' => null, // Accessible by all authenticated users
            ],

            // Academic Structure
            [
                'type' => 'dropdown',
                'label' => 'Academic Structure',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
                'can' => ['manage.structure'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'Structure Manager',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />',
                        'route' => 'admin.manager.structure',
                        'can' => 'view.courses',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Academic Calendar',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
                        'route' => 'admin.manager.calendar',
                        'can' => 'view.courses',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Course Catalog',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />',
                        'route' => 'admin.courses.index',
                        'can' => 'view.courses',
                    ],
                ],
            ],

            // People Management (Users)
            [
                'type' => 'dropdown',
                'label' => 'People',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                'can' => ['view.students', 'view.instructors'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'Students',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
                        'route' => 'admin.students.index',
                        'can' => 'view.students',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Instructors',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
                        'route' => 'admin.instructors.index',
                        'can' => 'view.instructors',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'User Accounts',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />',
                        'route' => 'admin.users.index',
                        'can' => 'view.users',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Manage Users',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                        'route' => 'admin.users.management',
                        'can' => 'view.users',
                    ],
                ],
            ],

            // Scheduling & Enrollment
            [
                'type' => 'dropdown',
                'label' => 'Operations',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />',
                'can' => ['view.schedule', 'batch.enroll'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'Class Schedule',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
                        'route' => 'admin.academic.schedule',
                        'can' => 'view.schedule',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Batch Enrollment',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
                        'route' => 'admin.academic.batch-enroll',
                        'can' => 'batch.enroll',
                    ],
                ],
            ],

            // Finance
            [
                'type' => 'dropdown',
                'label' => 'Financials',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'can' => ['view.invoices'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'Invoices & Payments',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />',
                        'route' => 'admin.finance.invoices',
                        'can' => 'view.invoices',
                    ],
                ],
            ],

            // System Settings
            [
                'type' => 'dropdown',
                'label' => 'Settings',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                'can' => ['view.roles', 'view.system-configs'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'Dictionaries',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />',
                        'route' => 'admin.settings.dictionaries',
                        'can' => 'view.system-configs',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Global Config',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />',
                        'route' => 'admin.settings.system',
                        'can' => 'view.system-configs',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Roles',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
                        'route' => 'admin.roles.index',
                        'can' => 'view.roles',
                    ],
                    // Permission (Key)
                    [
                        'type' => 'link',
                        'label' => 'Permissions',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.543 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />',
                        'route' => 'admin.permissions.index',
                        'can' => 'view.roles',
                    ],
                ],
            ],

            // === INSTRUCTOR SECTION ===
            [
                'type' => 'heading',
                'label' => 'Faculty Tools',
                'can' => ['view.gradebook', 'instructor'],
            ],
            [
                'type' => 'link',
                'label' => 'My Classes',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />',
                'route' => 'instructor.dashboard',
                'can' => ['view.gradebook', 'instructor'],
            ],

            // === STUDENT SECTION ===
            [
                'type' => 'heading',
                'label' => 'Student Portal',
                'can' => null, // Only visible to Students
            ],
            [
                'type' => 'link',
                'label' => 'My Dashboard',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
                'route' => 'academic.dashboard',
                'can' => null,
            ],
            [
                'type' => 'link',
                'label' => 'Weekly Schedule',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
                'route' => 'academic.schedule',
                'can' => null,
            ],
            [
                'type' => 'link',
                'label' => 'Transcript',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                'route' => 'academic.transcript',
                'can' => null,
            ],
            [
                'type' => 'link',
                'label' => 'Financials',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'route' => 'academic.finance',
                'can' => null,
            ],
        ];
    @endphp

    {{-- 3. NAVIGATION MENU --}}
    <nav class="flex-1 overflow-y-auto no-scrollbar py-0 px-3 space-y-1">
        @foreach ($menuItems as $item)
            {{-- We don't need to pass :collapsed, child can access sidebarCollapsed directly --}}
            <x-sidebar-menu-item :item="$item" />
        @endforeach
    </nav>

    {{-- 4. FOOTER --}}
    <div class="p-3 border-t border-gray-200 dark:border-gray-700 shrink-0">
        <form method="POST"
            action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center w-full p-2 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors group">
                <svg class="w-5 h-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span x-show="!sidebarCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap">
                    Log Out
                </span>

                {{-- Tooltip --}}
                <div x-show="sidebarCollapsed"
                    class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap pointer-events-none">
                    Log Out
                </div>
            </button>
        </form>
    </div>
</div>
