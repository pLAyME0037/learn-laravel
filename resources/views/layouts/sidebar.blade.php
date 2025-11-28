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
                'can' => ['view.asAdmin'],
            ],

            // Academic Record (Document Text)
            [
                'type' => 'dropdown',
                'label' => 'Academic Records',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                'can' => ['view.academic-records', 'create.academic-records'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Academic Records',
                        'route' => 'admin.academic-records.index',
                        'can' => 'view.academic-records',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Add Academic Record',
                        'route' => 'admin.academic-records.create',
                        'can' => 'create.academic-records',
                    ],
                ],
            ],

            // Academic Year (Calendar)
            [
                'type' => 'link',
                'label' => 'Academic Years',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
                'route' => 'admin.academic-years.index',
                'can' => 'view.academic-years',
            ],

            // Attendance (Clipboard Check)
            [
                'type' => 'link',
                'label' => 'Attendance',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />',
                'route' => 'admin.attendances.index',
                'can' => ['view.attendances'],
            ],

            // Audit Log (Eye / Search File)
            [
                'type' => 'link',
                'label' => 'Audit Log',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />', // Kept generic or swap to Eye: d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                'route' => 'admin.audit-logs.index',
                'can' => ['view.audit-logs'],
            ],

            // Classroom (Office Building)
            [
                'type' => 'dropdown',
                'label' => 'Classroom',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
                'can' => ['view.classrooms'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Classrooms',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
                        'route' => 'admin.classrooms.index',
                        'can' => ['view.classrooms'],
                    ],
                    // Class Schedule (Clock)
                    [
                        'type' => 'link',
                        'label' => 'Class Schedule',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
                        'route' => 'admin.class-schedules.index',
                        'can' => ['view.class-schedules'],
                    ],
                ],
            ],

            // Contact Detail (Identification / Address Book)
            [
                'type' => 'link',
                'label' => 'Contact Detail',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />',
                'route' => 'admin.contact-details.index',
                'can' => ['view.contact-details'],
            ],

            // Credit Score (Chart Bar)
            [
                'type' => 'link',
                'label' => 'Credit Score',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />',
                'route' => 'admin.credit-scores.index',
                'can' => ['view.credit-scores'],
            ],

            // Degree (Academic Cap)
            [
                'type' => 'link',
                'label' => 'Degree',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422A12.083 12.083 0 0118 20.5a2 2 0 01-2 1.5H8a2 2 0 01-2-1.5c0-3.98 1.98-7.98 1.84-9.922L12 14z" />',
                'route' => 'admin.degrees.index',
                'can' => ['view.degrees'],
            ],

            // Enrollment (Document Add)
            [
                'type' => 'link',
                'label' => 'Enrollment',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                'route' => 'admin.enrollments.index',
                'can' => ['view.enrollments'],
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
                        'label' => 'All Faculty',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                        'route' => 'admin.faculties.index',
                        'can' => ['view.faculties'],
                    ],
                    // Department (Office Structure)
                    [
                        'type' => 'link',
                        'label' => 'All Department',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
                        'route' => 'admin.departments.index',
                        'can' => 'view.departments',
                    ],

                    // Major (Bookmark)
                    [
                        'type' => 'link',
                        'label' => 'All Majors',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />',
                        'route' => 'admin.majors.index',
                        'can' => 'view.majors',
                    ],
                ],
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

            // Payment (Cash / Dollar)
            [
                'type' => 'link',
                'label' => 'Payments',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />',
                'route' => 'admin.payments.index',
                'can' => ['view.payments', 'create.payments'],
            ],

            // Programs (Book Open)
            [
                'type' => 'dropdown',
                'label' => 'Programs',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />',
                'can' => ['view.programs', 'create.programs'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Programs',
                        'route' => 'admin.programs.index',
                        'can' => 'view.programs',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Add Program',
                        'route' => 'admin.programs.create',
                        'can' => 'create.programs',
                    ],
                    // Courses (Collection of Books)
                    [
                        'type' => 'dropdown',
                        'label' => 'Courses',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
                        'can' => ['view.courses'],
                        'children' => [
                            [
                                'type' => 'link',
                                'label' => 'Courses',
                                'route' => 'admin.courses.index',
                                'can' => ['view.courses'],
                            ],
                            // Course Prerequisite (Link / Chain)
                            [
                                'type' => 'link',
                                'label' => 'Course Prerequisite',
                                'icon' =>
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />',
                                'route' => 'admin.course-prerequisites.index',
                                'can' => ['view.course-prerequisites'],
                            ],
                        ],
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
                        'route' => 'admin.roles.index',
                        'can' => 'view.roles',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Role',
                        'route' => 'admin.roles.create',
                        'can' => 'create.roles',
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

            // Semester (Calendar)
            [
                'type' => 'link',
                'label' => 'Semester',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
                'route' => 'admin.semesters.index',
                'can' => ['view.semesters'],
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
                        'label' => 'Add User',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                        'route' => 'admin.users.create',
                        'can' => 'create.users',
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
