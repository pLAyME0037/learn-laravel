<!DOCTYPE html>
<html lang="en"
    class="h-full {{ session('theme', 'light') === 'dark' ? 'dark' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token"
        content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - School Manage</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        <!-- Sidebar -->
        <x-sidebar :collapsed="session('sidebar_collapsed', false)" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <x-header :title="$pageTitle ?? 'Dashboard'" />

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
