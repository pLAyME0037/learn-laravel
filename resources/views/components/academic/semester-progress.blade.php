@props(['percent'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Semester Progress</h3>
        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $percent }}%</span>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700 mb-6">
        <div class="bg-indigo-600 h-4 rounded-full transition-all duration-1000 ease-out"
            style="width: {{ $percent }}%"></div>
    </div>

    <!-- Mini Stats grid for context -->
    <div class="grid grid-cols-2 gap-4">
        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Weeks Completed</p>
            <p class="font-bold text-gray-800 dark:text-gray-200">12</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Weeks Remaining</p>
            <p class="font-bold text-gray-800 dark:text-gray-200">4</p>
        </div>
    </div>
</div>
