@props(['student'])

<div
    class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-500 shadow-xl dark:from-indigo-900 dark:to-blue-800">
    <!-- Decorative Circle -->
    <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-white opacity-10 blur-3xl"></div>

    <div class="relative p-8 text-white">
        <div class="flex items-center space-x-4 mb-4">
            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                <svg class="w-8 h-8"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>
            </div>
            <span class="text-indigo-100 font-medium tracking-wide uppercase">
               Current Academic Program
            </span>
        </div>

        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">
            {{ $student->program->name ?? 'Program Not Assigned' }}
        </h1>

        <p class="text-lg text-indigo-100 font-light flex items-center">
            <svg class="w-5 h-5 mr-2"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                </path>
            </svg>
            {{ $student->department->name ?? 'Department Not Assigned' }}
            </h1>
    </div>
</div>
