<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1">
    <title>Academic Programs | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 font-sans">

    <!-- Navbar (Simplified) -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/"
                        class="font-bold text-xl text-indigo-600">SchulSYS</a>
                </div>
                <div class="flex items-center">
                    <a href="{{ route('login') }}"
                        class="text-sm font-medium text-gray-500 hover:text-gray-900">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="bg-indigo-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl font-extrabold text-white sm:text-4xl">
                Explore Our Programs
            </h1>
            <p class="mt-4 text-xl text-indigo-100">
                Discover the right path for your future career.
            </p>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="space-y-12">
            @foreach ($faculties as $faculty)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800">{{ $faculty->name }}</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($faculty->departments as $dept)
                            <div class="space-y-3">
                                <h3 class="font-semibold text-indigo-600 uppercase text-sm tracking-wide">
                                    {{ $dept->name }}
                                </h3>
                                <ul class="space-y-2">
                                    @foreach ($dept->majors as $major)
                                        @foreach ($major->programs as $program)
                                            <li class="text-gray-600 text-sm flex items-start">
                                                <svg class="h-5 w-5 text-green-400 mr-2"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                {{ $program->name }}
                                            </li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</body>

</html>
