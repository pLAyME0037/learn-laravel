<!DOCTYPE html>
<html lang="en"
    class="h-full {{ session('theme', 'dark') === 'light' ? 'light' : 'device' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token"
        content="{{ csrf_token() }}">
    @php $title = request()->route()?->getName(); @endphp
    <title>{{ $title ?? 'Dashboard' }} - School Manage</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
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
    @if (session('success'))
        <x-flash-message type="success" :message="session('success')" />
    @endif
    @if (session('error'))
        <x-flash-message type="error" :message="session('error')" />
    @endif
    <div class="flex h-screen">
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            @include('layouts.navigation')
            <!-- Page Content -->
            <div class="flex flex-1 overflow-hidden">
                <div class="flex flex-shrink-0 overflow-y-auto">
                    @include('layouts.sidebar');
                </div>
                <main class="flex-1 overflow-y-auto p-0">
                    <div class="py-6">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')
    @livewire('wire-elements-modal')
    @livewireScripts
</body>

</html>
