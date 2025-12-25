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

    {{-- 1. LOGO AREA --}}
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
        {{-- Toggle Button (Calls parent function) --}}
        <button @click="toggleSidebar()"
            class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
            <svg class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    {{-- 2. USER PROFILE (Compact) --}}
    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 shrink-0">
        <div class="flex items-center p-3"
            :class="sidebarCollapsed ? 'justify-center' : 'gap-3'">
            <div class="relative shrink-0">
                <img src="{{ Auth::user()->profile_picture_url ?? asset('images/default-avatar.png') }}"
                    class="w-8 h-8 rounded-full object-cover ring-2 ring-white dark:ring-gray-700"
                    alt="Avatar">
                <span
                    class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
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
            // Main Section
            [
                'type' => 'heading',
                'label' => 'Main',
            ],

            // Dashboard (Layout Grid)
            [
                'type' => 'link',
                'label' => 'Dashboard',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
                'route' => 'dashboard',
                'can' => null,
            ],

            // Admin Section
            [
                'type' => 'heading',
                'label' => 'Admin',
                'can' => ['Admin'],
            ],

            // Admin Dashboard
            [
                'type' => 'link',
                'label' => 'Administrator Dashboard',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
                'route' => 'admin.dashboard',
                'can' => null,
            ],

            // Faculty (Library / University)
            [
                'type' => 'dropdown',
                'label' => 'Faculty',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                'can' => ['view.faculties'],
                'children' => [
                    // Faculty
                    [
                        'type' => 'link',
                        'label' => 'Structure',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                        'route' => 'admin.manager.structure',
                        'can' => ['view.faculties'],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Calender',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                        'route' => 'admin.manager.calender',
                        'can' => ['view.faculties'],
                    ],
                    // Courses
                    [
                        'type' => 'link',
                        'label' => 'Courses',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
                        'route' => 'admin.courses.index',
                        'can' => ['view.courses'],
                    ],
                ],
            ],            
            
            // Academic
            [
                'type' => 'dropdown',
                'label' => 'Academic',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                'can' => ['view.faculties'],
                'children' => [
                    // Faculty
                    [
                        'type' => 'link',
                        'label' => 'Schdule',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                        'route' => 'admin.academic.schedule',
                        'can' => ['view.faculties'],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Batch Enrollment',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                        'route' => 'admin.academic.batch-enroll',
                        'can' => 'view.academic-records',
                    ],
                ],
            ],

            // Roles & Permission (Shield Check)
            [
                'type' => 'dropdown',
                'label' => 'Roles & Permission',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
                'can' => ['view.roles', 'create.roles'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Roles',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.543 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />',
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
                        'can' => 'manage.permissions',
                    ],
                ],
            ],

            // System Configs (Cog / Gear)
            [
                'type' => 'link',
                'label' => 'System Configs',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                'route' => 'admin.system_configs.index',
                'can' => ['view.system_configs'],
            ],

            // Transaction Ledger (Calculator / List)
            [
                'type' => 'link',
                'label' => 'Transaction Ledger',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />',
                'route' => 'admin.transaction-ledgers.index',
                'can' => ['view.transaction-ledgers'],
            ],

            // Users (User Group)
            [
                'type' => 'dropdown',
                'label' => 'Users',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                'can' => ['view.users'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Users',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                        'route' => 'admin.users.index',
                        'can' => 'view.users',
                    ],
                    // Instructor (Presentation / Teacher)
                    [
                        'type' => 'link',
                        'label' => 'Instructor',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />',
                        'route' => 'admin.instructors.index',
                        'can' => ['view.instructors'],
                    ],
                    // Student (User)
                    [
                        'type' => 'link',
                        'label' => 'Student',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                        'route' => 'admin.students.index',
                        'can' => ['view.students'],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Manage Users',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                        'route' => 'admin.users.management',
                        'can' => 'edit-access.users',
                    ],
                ],
            ],

            // Admin Tool
            [
                'type' => 'dropdown',
                'label' => 'Tools',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                'can' => ['view.users'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'Dictionary',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                        'route' => 'admin.settings.dictionaries',
                        'can' => 'view.academic-records',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Setting',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
                        'route' => 'admin.settings.system',
                        'can' => 'view.academic-records',
                    ],
                    // Login History (Clock)
                    [
                        'type' => 'link',
                        'label' => 'Login History',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
                        'route' => 'admin.login-histories.index',
                        'can' => 'view.login-histories',
                    ],
                ],
            ],

            // Finance Section
            [
                'type' => 'heading',
                'label' => 'Finance',
                'can' => null,
            ],
            
            // Finance
            [
                'type' => 'dropdown',
                'label' => 'Finance',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                'can' => ['view.faculties'],
                'children' => [
                    // Faculty
                    [
                        'type' => 'link',
                        'label' => 'Invoice',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                        'route' => 'admin.finance.invoices',
                        'can' => ['view.faculties'],
                    ],
                ],
            ],

            // Instructor Section
            [
                'type' => 'heading',
                'label' => 'Instrutor',
                'can' => null,
            ],

            // Instructor Dashboard
            [
                'type' => 'link',
                'label' => 'Instructor Dashboard',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
                'route' => 'instructor.dashboard',
                'can' => null,
            ],

            // Instructor
            [
                'type' => 'dropdown',
                'label' => 'Instructor',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                'can' => null,
                'children' => [
                    // PlaceHolder
                    [
                        'type' => 'link',
                        'label' => 'PlaceHolder',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                        'route' => 'instructor.dashboard',
                        'can' => null,
                    ],
                ],
            ],

            // Student Section
            [
                'type' => 'heading',
                'label' => 'Student',
                'can' => null,
            ],

            // Student Dashboard
            [
                'type' => 'link',
                'label' => 'Academic Dashboard',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
                'route' => 'academic.dashboard',
                'can' => null,
            ],
        ];
    @endphp

    {{-- 3. NAVIGATION MENU --}}
    <nav class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-1">
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
