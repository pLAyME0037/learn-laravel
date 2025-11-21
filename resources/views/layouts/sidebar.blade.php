@props(['collapsed' => false])

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

<div x-data="{
    collapsed: {{ $collapsed ? 'true' : 'false' }},
    sidebarToggle() {
        this.collapsed = !this.collapsed;
        // Dispatch event for main content to adjust
        window.dispatchEvent(new CustomEvent('sidebar-toggle', { detail: this.collapsed }));
    }
}"
    class="h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-all duration-300 z-30"
    :class="collapsed ? 'w-16' : 'w-64'">

    {{-- 1. LOGO AREA --}}
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
        
        <button @click="sidebarToggle()"
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
            :class="collapsed ? 'justify-center' : 'gap-3'">
            <div class="relative shrink-0">
                <img src="{{ Auth::user()->profile_picture_url ?? asset('images/default-avatar.png') }}"
                    class="w-8 h-8 rounded-full object-cover ring-2 ring-white dark:ring-gray-700"
                    alt="Avatar">
                <span
                    class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
            </div>

            <div x-show="!collapsed"
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
            // Dashboard
            [
                'type' => 'link',
                'label' => 'Dashboard',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
                'route' => 'dashboard',
                'can' => null,
            ],
            // A
            // Admin Section
            [
                'type' => 'heading',
                'label' => 'Admin',
                'can' => ['view.asAdmin'],
            ],
            // Academic Record
            [
                'type' => 'dropdown',
                'label' => 'Academic Records',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />',
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
            // Academic Year
            [
                'type' => 'link',
                'label' => 'Academic Years',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422A12.083 12.083 0 0118 20.5a2 2 0 01-2 1.5H8a2 2 0 01-2-1.5c0-3.98 1.98-7.98 1.84-9.922L12 14z" />',
                'route' => 'admin.academic-years.index',
                'can' => 'view.academic-years',
            ],
            // Attendent
            [
                'type' => 'link',
                'label' => 'Attendent',
                'icon' => '<path stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />',
                'route' => 'admin.attendances.index',
                'can' => ['view.attendances'],
            ],
            // Audit Log
            [
                'type' => 'link',
                'label' => 'Audit Log',
                'icon' => '<path stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />',
                'route' => 'admin.audit-logs.index',
                'can' => ['view.audit-logs'],
            ],
            // B
            // C
            // Classroom
            [
                'type' => 'dropdown',
                'label' => 'Classroom',
                'icon' => '<path 
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" 
                            />',
                'can' => ['view.classrooms'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Classroom',
                        'icon' => '<path 
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" 
                            />',
                        'route' => 'admin.classrooms.index',
                        'can' => ['view.classrooms'],
                    ],
                    // Class Schedule
                    [
                        'type' => 'link',
                        'label' => 'Class Schedule',
                        'icon' => '<path 
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" 
                            />',
                        'route' => 'admin.class-schedules.index',
                        'can' => ['view.class-schedules'],
                    ],
                ],
            ],
            // Contact Detail
            [
                'type' => 'link',
                'label' => 'Contact Detail',
                'icon' => '<path 
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" 
                            />',
                'route' => 'admin.contact-details.index',
                'can' => ['view.contact-details'],
            ],
            // Cridit Score
            [
                'type' => 'link',
                'label' => 'Cridit Score',
                'icon' => '<path 
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" 
                            />',
                'route' => 'admin.credit-scores.index',
                'can' => ['view.credit-scores'],
            ],
            // D
            // Degree
            [
                'type' => 'link',
                'label' => 'Degree',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                'route' => 'admin.degrees.index',
                'can' => ['view.degrees'],
            ],
            // E
            // Enrollment
            [
                'type' => 'link',
                'label' => 'Enrollment',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                'route' => 'admin.enrollments.index',
                'can' => ['view.enrollments'],
            ],
            // F
            // Faculty
            [
                'type' => 'dropdown',
                'label' => 'Faculty',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                'can' => ['view.faculties'],
                'children' => [
                    // Faculty
                    [
                        'type' => 'link',
                        'label' => 'All Faculty',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                        'route' => 'admin.faculties.index',
                        'can' => ['view.faculties'],
                    ],
                    // Department
                    [
                        'type' => 'dropdown',
                        'label' => 'Department',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                        'can' => ['view.departments', 'create.departments'],
                        'children' => [
                            // Department
                            [
                                'type' => 'link',
                                'label' => 'All Department',
                                'icon' =>
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                                'route' => 'admin.departments.index',
                                'can' => 'view.departments',
                            ],
                            [
                                'type' => 'link',
                                'label' => 'New Department',
                                'icon' =>
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                                'route' => 'admin.departments.create',
                                'can' => 'create.departments',
                            ],
                            // Major
                            [
                                'type' => 'dropdown',
                                'label' => 'Majors',
                                'icon' =>
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />',
                                'can' => ['view.majors', 'create.majors'],
                                'children' => [
                                    [
                                        'type' => 'link',
                                        'label' => 'All Majors',
                                        'route' => 'admin.majors.index',
                                        'can' => 'view.majors',
                                    ],
                                    [
                                        'type' => 'link',
                                        'label' => 'Add Major',
                                        'route' => 'admin.majors.create',
                                        'can' => 'create.majors',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            // G
            // H
            // I
            // Instructor
            [
                'type' => 'link',
                'label' => 'Instructor',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                'route' => 'admin.instructors.index',
                'can' => ['view.instructors'],
            ],
            // J
            // K
            // L
            // Login History
            [
                'type' => 'link',
                'label' => 'Login History',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />',
                'route' => 'admin.login-histories.index',
                'can' => 'view.login-histories',
            ],
            // M
            // N
            // O
            // P
            // Payment
            [
                'type' => 'link',
                'label' => 'Payments',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />',
                'route' => 'admin.payments.index',
                'can' => ['view.payments', 'create.payments'],
            ],
            // Program
            [
                'type' => 'dropdown',
                'label' => 'Programs',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
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
                    // Courses
                    [
                        'type' => 'dropdown',
                        'label' => 'Courses',
                        'icon' => '<path 
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" 
                        />',
                        'can' => ['view.courses'],
                        'children' => [
                            [
                                'type' => 'link',
                                'label' => 'Courses',
                                'icon' => '<path 
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" 
                                />',
                                'route' => 'admin.courses.index',
                                'can' => ['view.courses'],
                            ],
                            // Course Prerequisite
                            [
                                'type' => 'link',
                                'label' => 'Course Prerequisite',
                                'icon' => '<path 
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" 
                                />',
                                'route' => 'admin.course-prerequisites.index',
                                'can' => ['view.course-prerequisites'],
                            ],
                        ],
                    ],
                ],
            ],

            // Q
            // R
            // Roles
            [
                'type' => 'dropdown',
                'label' => 'Roles & Permission',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-2A2 2 0 0113 18V6a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2zM10 18H8a2 2 0 01-2-2V8a2 2 0 012-2h2m-2 8h4" />',
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
                    // Permission
                    [
                        'type' => 'link',
                        'label' => 'Permissions',
                        'icon' => '<path 
                            stroke-linecap="round" 
                            stroke-linejoin="round" 
                            stroke-width="2" 
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" 
                        />',
                        'route' => 'admin.permissions.index',
                        'can' => 'manage.permissions',
                    ],
                ],
            ],
            // S
            // Semester
            [
                'type' => 'link',
                'label' => 'Semester',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                'route' => 'admin.semesters.index',
                'can' => ['view.semesters'],
            ],
            // Student
            [
                'type' => 'dropdown',
                'label' => 'Student',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                'route' => 'admin.students.index',
                'can' => ['view.students'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'Student',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                        'route' => 'admin.students.index',
                        'can' => ['view.students'],
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Student',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                        'route' => 'admin.students.create',
                        'can' => ['create.students'],
                    ],
                ],
            ],
            // System Configs
            [
                'type' => 'link',
                'label' => 'System Configs',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                'route' => 'admin.system_configs.index',
                'can' => ['view.system_configs'],
            ],
            // T
            // Transaction Ledger
            [
                'type' => 'link',
                'label' => 'Transaction Ledger',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                'route' => 'admin.transaction-ledgers.index',
                'can' => ['view.transaction-ledgers'],
            ],
            // U
            // Users
            [
                'type' => 'dropdown',
                'label' => 'Users',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                'can' => ['view.users'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Users',
                        'route' => 'admin.users.index',
                        'can' => 'view.users',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Add User',
                        'route' => 'admin.users.create',
                        'can' => 'create.users',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Manage Users',
                        'route' => 'admin.users.management',
                        'can' => 'edit-access.users',
                    ],
                ],
            ],
            // V
            // W
        ];
    @endphp

    {{-- 3. NAVIGATION MENU --}}
    <nav class="flex-1 overflow-y-auto no-scrollbar py-4 px-3 space-y-1">
        @foreach ($menuItems as $item)
            <x-sidebar-menu-item :item="$item"
                :collapsed="$collapsed" />
        @endforeach
    </nav>

    {{-- 4. FOOTER (Logout, Settings, etc) --}}
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
                <span x-show="!collapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap">Log Out</span>

                {{-- Tooltip for collapsed state --}}
                <div x-show="collapsed"
                    class="absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap pointer-events-none">
                    Log Out
                </div>
            </button>
        </form>
    </div>
</div>
