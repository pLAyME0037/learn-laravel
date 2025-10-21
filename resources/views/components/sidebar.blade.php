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

    <nav class="mt-0">
        <div x-show="!collapsed"
            class="px-6 py-2 dark:bg-blue-900/20 border-r-4 bg-blue-50">
            <h2 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">
                Main
            </h2>
        </div>

        <ul>
            <li>
                <a href="{{ route('dashboard') }}"
                    class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                    :class="collapsed ? 'justify-center' : ''">
                    <svg class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span x-show="!collapsed"
                        class="mx-3">
                        Dashboard
                    </span>
                </a>
            </li>

            @canany(['users.view', 'users.create', 'departments.view', 'departments.create', 'audit.view'])
                {{-- Admin Section --}}
                <div x-show="!collapsed"
                    class="px-6 py-2 mt-0 dark:bg-blue-900/20 border-r-4 bg-blue-50">
                    <h2 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">
                        Admin
                    </h2>
                </div>

                @canany(['users.view', 'users.create'])
                    {{-- Users --}}
                    <li x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                            :class="collapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <template x-if="!collapsed">
                                <span class="mx-3 text-left flex-1">
                                    Users
                                </span>
                            </template>
                            <template x-if="!collapsed">
                                <svg :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </template>
                        </button>

                        <div x-show="!collapsed && open"
                            x-collapse
                            class="bg-gray-50 dark:bg-gray-700">
                            @can('users.view')
                                <a href="{{ route('admin.users.index') }}"
                                    class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    All Users
                                </a>
                            @endcan
                            @can('users.create')
                                <a href="{{ route('admin.users.create') }}"
                                    class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Add User
                                </a>
                            @endcan
                        </div>
                    </li>
                @endcanany

                @canany(['departments.view', 'departments.create'])
                    {{-- Department --}}
                    <li x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                            :class="collapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 21h18 M7 21V9a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v12 M7 13h10M7 17h10 M10 9h.01M14 9h.01" />
                            </svg>
                            <template x-if="!collapsed">
                                <span class="mx-3 text-left flex-1">
                                    Department
                                </span>
                            </template>
                            <template x-if="!collapsed">
                                <svg :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </template>
                        </button>

                        <div x-show="!collapsed && open"
                            x-collapse
                            class="bg-gray-50 dark:bg-gray-700">
                            @can('departments.view')
                                <a href="{{ route('admin.departments.index') }}"
                                    class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    All Department
                                </a>
                            @endcan
                            @can('departments.create')
                                <a href="{{ route('admin.departments.create') }}"
                                    class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    New Department
                                </a>
                            @endcan
                        </div>
                    </li>
                @endcanany

                @can('audit.view')
                    {{-- Login History --}}
                    <li x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                            :class="collapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3" />
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
                            </svg>
                            <template x-if="!collapsed">
                                <span class="mx-3 text-left flex-1">
                                    Login History
                                </span>
                            </template>
                            <template x-if="!collapsed">
                                <svg :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </template>
                        </button>

                        <div x-show="!collapsed && open"
                            x-collapse
                            class="bg-gray-50 dark:bg-gray-700">
                            <a href="{{ route('admin.login-history.index') }}"
                                class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                All Login History
                            </a>
                        </div>
                    </li>
                @endcan

                {{-- Role Management --}}
                @canany(['roles.view', 'roles.create'])
                    <li x-data="{ open: false }">
                        <button @click="open = !open"
                            class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                            :class="collapsed ? 'justify-center' : ''">
                            <svg class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h-2A2 2 0 0113 18V6a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2zM10 18H8a2 2 0 01-2-2V8a2 2 0 012-2h2m-2 8h4" />
                            </svg>
                            <template x-if="!collapsed">
                                <span class="mx-3 text-left flex-1">
                                    Roles
                                </span>
                            </template>
                            <template x-if="!collapsed">
                                <svg :class="{ 'rotate-180': open }"
                                    class="w-4 h-4 transition-transform"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </template>
                        </button>

                        <div x-show="!collapsed && open"
                            x-collapse
                            class="bg-gray-50 dark:bg-gray-700">
                            @can('roles.view')
                                <a href="{{ route('admin.roles.index') }}"
                                    class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    All Roles
                                </a>
                            @endcan
                            @can('roles.create')
                                <a href="{{ route('admin.roles.create') }}"
                                    class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Create Role
                                </a>
                            @endcan
                        </div>
                    </li>
                @endcanany

                {{-- Permission Management --}}
                @can('permissions.view')
                    <li>
                        <a href="{{ route('admin.permissions.index') }}"
                            class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                            :class="collapsed ? 'justify-center' : ''">
                            <svg class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-show="!collapsed"
                                class="mx-3">
                                Permissions
                            </span>
                        </a>
                    </li>
                @endcan

                {{-- Academic Years --}}
                @can('academic_years.view')
                    <li>
                        <a href="{{ route('admin.academic_years.index') }}"
                            class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                            :class="collapsed ? 'justify-center' : ''">
                            <svg class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422A12.083 12.083 0 0118 20.5a2 2 0 01-2 1.5H8a2 2 0 01-2-1.5c0-3.98 1.98-7.98 1.84-9.922L12 14z" />
                            </svg>
                            <span x-show="!collapsed"
                                class="mx-3">
                                Academic Years
                            </span>
                        </a>
                    </li>
                @endcan
            @endcanany
        </ul>
    </nav>
</div>
