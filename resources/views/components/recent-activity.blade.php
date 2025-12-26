@props(['title' => 'Recent Activity'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow transition-colors duration-300">
    <div class="px-6 py-4 border-b dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            {{ $title }}
        </h3>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            <!-- Mock Data (Replace with AuditLog query later) -->
            <div class="flex items-center">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        System backup completed
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        1 hour ago
                    </p>
                </div>
            </div>
            <div class="flex items-center">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        50 students enrolled (Batch)
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        3 hours ago
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
