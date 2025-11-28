<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    {{-- 1. Apply 'dark' class immediately via PHP to prevent white flash --}}
    class="h-full {{ session('theme', 'system') === 'dark' ? 'dark' : '' }}"
    x-data="{
        darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
    }"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token"
        content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'Schul SYS') }}</title>
    <link rel="icon"
        href="{{ asset('favicon.ico') }}">

    {{-- Alpine Cloak --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- 2. Inline Theme Script (No dependencies required) --}}
    <script>
        // Immediately check theme preference to avoid Flash of Unstyled Content (FOUC)
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="h-full bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300"
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
        }
    }">

    <!-- Main Content Area -->
    <div class="flex flex-col h-screen overflow-hidden">
        <header class="flex-shrink-0 z-40">
            {{-- Top Navigation --}}
            @include('layouts.navigation')
        </header>
        <div class="flex flex-1 overflow-hidden relative">
            {{-- Sidebar (Fixed Left) --}}
            <aside
                class="flex-shrink-0 hidden md:flex flex-col border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 transition-all duration-300"
                :class="sidebarCollapsed ? 'w-16' : 'w-64'">
                @include('layouts.sidebar')
            </aside>
            <main class="flex-1 overflow-y-auto focus:outline-none bg-gray-100 dark:bg-gray-900 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700">

                {{-- Flash Messages --}}
                @if (session('success'))
                    <x-flash-message type="success"
                        :message="session('success')" />
                @endif
                @if (session('error'))
                    <x-flash-message type="error"
                        :message="session('error')" />
                @endif

                <!-- Scrollable Content -->
                <div class="py-6 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
    @livewire('wire-elements-modal')
    @livewireScripts
</body>

</html>
