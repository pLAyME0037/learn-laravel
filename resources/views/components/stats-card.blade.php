@props(['title', 'value', 'icon', 'color' => 'blue'])

@php
    $colors = [
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'dark_bg' => 'dark:bg-blue-900/20', 'dark_text' => 'dark:text-blue-400'],
        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600', 'dark_bg' => 'dark:bg-green-900/20', 'dark_text' => 'dark:text-green-400'],
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'dark_bg' => 'dark:bg-purple-900/20', 'dark_text' => 'dark:text-purple-400'],
        'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600', 'dark_bg' => 'dark:bg-red-900/20', 'dark_text' => 'dark:text-red-400'],
    ];
    $colorConfig = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors duration-300">
    <div class="flex items-center">
        <div class="p-3 {{ $colorConfig['bg'] }} {{ $colorConfig['dark_bg'] }} rounded-lg">
            @if($icon === 'users')
                <svg class="w-6 h-6 {{ $colorConfig['text'] }} {{ $colorConfig['dark_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            @elseif($icon === 'currency')
                <svg class="w-6 h-6 {{ $colorConfig['text'] }} {{ $colorConfig['dark_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10a2 2 0 11-4 0 2 2 0 014 0zM6 18a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            @elseif($icon === 'shopping-bag')
                <svg class="w-6 h-6 {{ $colorConfig['text'] }} {{ $colorConfig['dark_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            @elseif($icon === 'trending-up')
                <svg class="w-6 h-6 {{ $colorConfig['text'] }} {{ $colorConfig['dark_text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            @endif
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $title }}</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $value }}</p>
        </div>
    </div>
</div>