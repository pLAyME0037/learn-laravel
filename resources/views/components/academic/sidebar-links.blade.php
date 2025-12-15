@props(['links'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="p-5 border-b border-gray-100 dark:border-gray-700">
        <h3 class="font-semibold text-gray-800 dark:text-gray-200">
            Quick Resources
        </h3>
    </div>
    <div class="p-2">
        @foreach ($links as $link)
            <a href="{{ $link['url'] }}"
                class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                <div
                    class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900 transition-colors">
                    <!-- Simple Dynamic Icon logic could go here, treating as generic for now -->
                    <svg class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                        </path>
                    </svg>
                </div>
                <span
                    class="text-sm font-medium text-gray-600 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                    {{ $link['title'] }}
                </span>
                <svg class="w-4 h-4 text-gray-300 ml-auto"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7">
                    </path>
                </svg>
            </a>
        @endforeach
    </div>
</div>
