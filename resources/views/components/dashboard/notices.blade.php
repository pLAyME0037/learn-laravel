@props(['notices'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">System Notices</h3>
    <div class="space-y-3">
        @foreach ($notices as $notice)
            <div
                class="p-3 rounded-lg flex items-start space-x-3 
                {{ $notice['type'] === 'warning' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400' : 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400' }}">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm">{{ $notice['message'] }}</p>
            </div>
        @endforeach
    </div>
</div>
