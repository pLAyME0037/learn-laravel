@props(['activities'])

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Recent Activity</h3>

    <div class="space-y-6 border-l-2 border-gray-100 dark:border-gray-700 ml-3 pl-6">
        @foreach ($activities as $activity)
            <div class="relative">
                <!-- Dot -->
                <span
                    class="absolute -left-[31px] top-1 flex h-4 w-4 items-center justify-center rounded-full bg-white dark:bg-gray-800 border-2 
                    {{ $activity['type'] === 'success' ? 'border-green-500' : '' }}
                    {{ $activity['type'] === 'info' ? 'border-blue-500' : '' }}
                    {{ $activity['type'] === 'warning' ? 'border-yellow-500' : '' }}
                    {{ $activity['type'] === 'neutral' ? 'border-gray-400' : '' }}
                ">
                    <span
                        class="h-1.5 w-1.5 rounded-full 
                        {{ $activity['type'] === 'success' ? 'bg-green-500' : '' }}
                        {{ $activity['type'] === 'info' ? 'bg-blue-500' : '' }}
                        {{ $activity['type'] === 'warning' ? 'bg-yellow-500' : '' }}
                        {{ $activity['type'] === 'neutral' ? 'bg-gray-400' : '' }}
                    "></span>
                </span>

                <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $activity['title'] }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $activity['desc'] }}</p>
                <span class="text-xs text-gray-400 mt-1 block">{{ $activity['date'] }}</span>
            </div>
        @endforeach
    </div>
</div>
