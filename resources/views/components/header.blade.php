@props(['title' => 'Dashboard'])

<header class="bg-white dark:bg-gray-800 shadow-sm">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ $title }}</h2>
        </div>

        <div class="flex items-center space-x-4">
            <!-- Theme Toggle -->
            <div class="relative"
                x-data="{ open: false }">
                <button @click="open = !open"
                    class="p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                    <svg class="w-6 h-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <div x-show="open"
                    @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg py-1 z-50">
                    <button onclick="window.themeController.setTheme('light')"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Light Mode
                    </button>
                    <button onclick="window.themeController.setTheme('dark')"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Dark Mode
                    </button>
                    <button onclick="window.themeController.setTheme('device')"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Device Setting
                    </button>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative"
                x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center space-x-2 focus:outline-none">
                    <img class="w-8 h-8 rounded-full"
                        src="https://via.placeholder.com/32"
                        alt="User">
                    <span
                        class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->name ?? 'John Doe' }}</span>
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
                </button>

                <div x-show="open"
                    @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg py-1 z-50">
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Edit Profile
                    </a>
                    <a href="#"
                        class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Settings
                    </a>
                    <div class="border-t dark:border-gray-600"></div>
                    <a href="{{ route('login') }}"
                        class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                        Login
                    </a>
                    <form method="POST"
                        action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
