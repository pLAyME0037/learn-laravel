<!DOCTYPE html>
<html lang="en"
    class="h-full {{ session('theme', 'dark') === 'light' ? 'light' : 'device' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token"
        content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - School Manage</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        // Initialize theme from session or localStorage
        function initTheme() {
            if (window.themeController) {
                const sessionTheme = '{{ session('theme') }}';
                const localTheme = localStorage.theme;

                if (sessionTheme) {
                    window.themeController.applyTheme(sessionTheme);
                } else if (localTheme) {
                    window.themeController.applyTheme(localTheme);
                } else {
                    window.themeController.applyTheme('device');
                }
            } else {
                setTimeout(initTheme, 50);
            }
        }
        document.addEventListener('DOMContentLoaded', initTheme);
    </script>
</head>

<body class="h-full bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    <div class="flex h-screen">
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <x-header :title="$pageTitle ?? 'Dashboard'" />
            <!-- Page Content -->
            <div class="flex flex-1 overflow-hidden">
                <div class="flex flex-shrink-0 overflow-y-auto">
                    @include('layouts/sidebar');
                </div>
                <main class="flex-1 overflow-y-auto p-3">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')
    @livewireScripts
</body>

</html>
