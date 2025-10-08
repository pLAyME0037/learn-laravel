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
                @<span>{{ Auth::user()->username }}</span>
            </div>
            <div class="font-mono text-xs text-gray-900 dark:text-white">
                {{ Auth::user()->email }}
            </div>
        </div>
    </div>

    <nav class="mt-6">
        <div x-show="!collapsed"
            class="px-6 py-2">
            <h2 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Main</h2>
        </div>

        <ul>
            <li>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 bg-blue-50 dark:bg-blue-900/20 border-r-4 border-blue-500"
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
                        class="mx-3">Dashboard
                    </span>
                </a>
            </li>

            <li x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                    :class="collapsed ? 'justify-center' : ''">
                    <svg class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <template x-if="!collapsed">
                        <span class="mx-3 text-left flex-1">Users</span>
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
                    <a href="#"
                        class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        All Users
                    </a>
                    <a href="#"
                        class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Add User
                    </a>
                </div>
            </li>
        </ul>
    </nav>
</div>
