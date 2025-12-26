@props(['title' => 'Dashboard'])

<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
    <div class="flex items-center justify-between px-4 py-3">

        <!-- LEFT: Toggle & Logo & Title -->
        <div class="flex items-center gap-4">

            {{-- TOGGLE BUTTON (Moves Sidebar State) --}}
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="w-6 h-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Logo (Optional if Sidebar has one, but good for Mobile) -->
            <div class="hidden md:flex items-center gap-2">
                <x-application-logo class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
                <span class="font-bold text-xl text-gray-800 dark:text-white tracking-tight">
                    Schul SYS
                </span>
            </div>

            <!-- Vertical Separator -->
            <div class="h-6 w-px bg-gray-300 dark:bg-gray-600 mx-2 hidden md:block"></div>

            <!-- Dynamic Page Title -->
            @php
                $currentRouteName = request()->route()?->getName();
                $autoTitle = $title; // Default to prop

                if ($currentRouteName && $title === 'Dashboard') {
                    // Smart Title Logic
                    $segments = explode('.', $currentRouteName);
                    $action = end($segments);
                    $resource = prev($segments); // e.g. 'students'

                    if (in_array($action, ['index', 'show', 'create', 'edit'])) {
                        $resourceName = ucfirst(str_replace(['-', '_'], ' ', $resource));
                        $actionName = ucfirst($action);

                        if ($action === 'index') {
                            $autoTitle = "$resourceName Management";
                        } elseif ($action === 'show') {
                            $autoTitle = "$resourceName Details";
                        } else {
                            $autoTitle = "$actionName $resourceName";
                        }
                    }
                }
            @endphp
            <h1 class="text-lg font-semibold text-gray-800 dark:text-white truncate">
                {{ $autoTitle }}
            </h1>
        </div>

        <!-- RIGHT: Actions -->
        <div class="flex items-center space-x-3">

            <!-- Theme Toggle -->
            <div x-data="{ open: false }"
                class="relative">
                <button @click="open = !open"
                    class="p-2 rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
                    <!-- Sun/Moon Icon based on state (Simplified generic icon here) -->
                    <svg class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                </button>
                <!-- Theme Dropdown (Keep your existing logic) -->
                <div x-show="open"
                    @click.away="open = false"
                    style="display: none;"
                    class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-700 rounded-lg shadow-lg py-1 border dark:border-gray-600 z-50">
                    <button onclick="window.themeController.setTheme('light')"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Light</button>
                    <button onclick="window.themeController.setTheme('dark')"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">Dark</button>
                    <button onclick="window.themeController.setTheme('device')"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">System</button>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }"
                class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-2 p-1 pr-3 rounded-full border border-transparent hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <x-profile-image src="{{ Auth::user()->profile_picture_url }}"
                        alt="{{ Auth::user()->name }}"
                        size="xs"
                        class="h-8 w-8" />
                    <span
                        class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ Auth::user()->name }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>

                <div x-show="open"
                    @click.away="open = false"
                    style="display: none;"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 border dark:border-gray-700 z-50">
                    <div class="px-4 py-2 border-b dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Signed in as
                        </p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                            {{ Auth::user()->email }}
                        </p>
                    </div>

                    <a href="{{ route('profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile
                        Settings
                    </a>

                    <form method="POST"
                        action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>
