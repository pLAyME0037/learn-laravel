@props(['collapsed' => false])

<div x-data="{ collapsed: {{ $collapsed ? 'true' : 'false' }} }"
    id="sidebar"
    class="bg-white dark:bg-gray-800 shadow-lg transition-all duration-300"
    :class="collapsed ? 'w-20' : 'w-64'">
    <!-- Logo and Hamburger -->
    <div class="p-6 border-b dark:border-gray-700 flex items-center"
        :class="collapsed ? 'justify-center' : 'justify-between'">
        <h1 x-show="!collapsed"
            class="text-2xl font-bold text-gray-800 dark:text-white">Schul SYS</h1>
        <button @click="collapsed = !collapsed; window.sidebarToggle()"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300"
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

    {{-- Profile Icon --}}
    <div class="flex items-center space-x-3 px-6 py-3 text-gray-700 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20 border-r-4 border-blue-500"
        x-show="!collapsed">
        <x-profile-image src="{{ Auth::user()->profile_picture_url }}"
            alt="{{ Auth::user()->username }}"
            size="lg" />
        <div class="text-left">
            <div class="font-semibold text-gray-900 dark:text-white">
                {{ Auth::user()->name }}
            </div>
            <div class="text-xs text-gray-500">
                {{ \Illuminate\Support\Str::limit(
                    '@' . Auth::user()->username . ' #' . Auth::user()->roles->pluck('name')->join(', '),
                    20,
                    '...',
                ) }}
            </div>
            <div class="font-mono text-xs text-gray-900 dark:text-white">
                {{ Auth::user()->email }}
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
            ],
            // Academic Record
            [
                'type' => 'dropdown',
                'label' => 'Academic Records',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />',
                'can' => ['academic_records.view', 'academic_records.create'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Academic Records',
                        'route' => 'admin.academic_records.index',
                        'can' => 'academic_records.view',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Add Academic Record',
                        'route' => 'admin.academic_records.create',
                        'can' => 'academic_records.create',
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
                'can' => 'academic_years.view',
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
                'can' => ['attendances.view', 'attendances.create'],
            ],
        // B
        // C
        // D
            // Department
            [
                'type' => 'dropdown',
                'label' => 'Department',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />',
                'can' => ['departments.view', 'departments.create'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Department',
                        'route' => 'admin.departments.index',
                        'can' => 'departments.view',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'New Department',
                        'route' => 'admin.departments.create',
                        'can' => 'departments.create',
                    ],
                ],
            ],
        // E
        // F
        // G
        // H
        // I
        // J
        // K
        // L
            // Login History
            [
                'type' => 'dropdown',
                'label' => 'Login History',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />',
                'can' => 'audit.view',
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Login History',
                        'route' => 'admin.login-histories.index',
                        'can' => 'audit.view',
                    ],
                ],
            ],
        // M
            // Major
            [
                'type' => 'dropdown',
                'label' => 'Majors',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />',
                'can' => ['majors.view', 'majors.create'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Majors',
                        'route' => 'admin.majors.index',
                        'can' => 'majors.view',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Add Major',
                        'route' => 'admin.majors.create',
                        'can' => 'majors.create',
                    ],
                ],
            ],
        // N
        // O
        // P
            // Permission
            [
                'type' => 'link',
                'label' => 'Permissions',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                'route' => 'admin.permissions.index',
                'can' => 'permissions.view',
            ],
            // Payment
            [
                'type' => 'dropdown',
                'label' => 'Payments',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />',
                'can' => ['payments.view', 'payments.create'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Payments',
                        'route' => 'admin.payments.index',
                        'can' => 'payments.view',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Add Payment',
                        'route' => 'admin.payments.create',
                        'can' => 'payments.create',
                    ],
                ],
            ],
            // Program
            [
                'type' => 'dropdown',
                'label' => 'Programs',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />',
                'can' => ['programs.view', 'programs.create'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Programs',
                        'route' => 'admin.programs.index',
                        'can' => 'programs.view',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Add Program',
                        'route' => 'admin.programs.create',
                        'can' => 'programs.create',
                    ],
                ],
            ],
        // Q
        // R
            // Roles
            [
                'type' => 'dropdown',
                'label' => 'Roles',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-2A2 2 0 0113 18V6a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2zM10 18H8a2 2 0 01-2-2V8a2 2 0 012-2h2m-2 8h4" />',
                'can' => ['roles.view', 'roles.create'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Roles',
                        'route' => 'admin.roles.index',
                        'can' => 'roles.view',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Create Role',
                        'route' => 'admin.roles.create',
                        'can' => 'roles.create',
                    ],
                ],
            ],
        // S
        // T
        // U
            // Users
            [
                'type' => 'dropdown',
                'label' => 'Users',
                'icon' =>
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />',
                'can' => ['users.view', 'users.create'],
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'All Users',
                        'route' => 'admin.users.index',
                        'can' => 'users.view',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Add User',
                        'route' => 'admin.users.create',
                        'can' => 'users.create',
                    ],
                ],
            ],
        // V
        // W
        // Y
        ];
    @endphp

    <nav class="mt-0">
        <ul>
            @foreach ($menuItems as $item)
                <x-sidebar-menu-item :item="$item"
                    :collapsed="$collapsed" />
            @endforeach
        </ul>
    </nav>
</div>
