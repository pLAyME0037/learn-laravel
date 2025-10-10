<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - School Manage</title>

    <script>
        // Prevents FOUC (Flash of Unstyled Content)
        (function() {
            const theme = localStorage.getItem('theme') || ('{{ session('theme', 'device') }}');
            if (theme === 'dark' || (theme === 'device' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    @livewireScripts
</body>

</html>
