@props(['item', 'level' => 0])

@php
    use Illuminate\Support\Facades\Gate;
    use Illuminate\Support\Facades\Route;

    // 1. Permission Check
    $hasPermission = true;
    if (isset($item['can'])) {
        $hasPermission = is_array($item['can']) ? Gate::any($item['can']) : Gate::allows($item['can']);
    }

    // 2. Active State Logic
    $isActive = false;
    if (isset($item['route']) && Route::currentRouteName() === $item['route']) {
        $isActive = true;
    }
    $hasActiveChild = false;
    if (isset($item['children'])) {
        foreach ($item['children'] as $child) {
            if (isset($child['route']) && Route::currentRouteName() === $child['route']) {
                $isActive = true;
                $hasActiveChild = true;
                break;
            }
        }
    }

    // 3. Indentation Logic
    // We convert PHP variables to Alpine classes dynamically
    $paddingClass = match ($level) {
        0 => 'pl-2',
        1 => 'pl-4',
        2 => 'pl-6',
        default => 'pl-2',
    };
@endphp

@if ($hasPermission)

    {{-- HEADING --}}
    @if ($item['type'] === 'heading')
        {{-- Use Alpine x-show instead of PHP if() --}}
        <div x-show="!sidebarCollapsed"
            class="mt-4 mb-2 px-3 transition-opacity duration-200">
            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                {{ $item['label'] }}
            </span>
        </div>
        {{-- Separator line when collapsed --}}
        <div x-show="sidebarCollapsed"
            class="mt-4 mb-2 border-t border-gray-200 dark:border-gray-700 mx-2"></div>


        {{-- LINK --}}
    @elseif ($item['type'] === 'link')
        <div class="relative group mb-1">
            <a href="{{ route($item['route']) }}"
                class="flex items-center w-full p-2 rounded-lg transition-all duration-200 ease-in-out
                      {{ $isActive
                          ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300'
                          : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200' }}"
                {{-- ALPINE CLASS BINDING REPLACING PHP LOGIC --}}
                :class="sidebarCollapsed ? 'justify-center px-2' : '{{ $paddingClass }}'">

                {{-- Icon --}}
                @if (isset($item['icon']))
                    <svg class="w-5 h-5 shrink-0 transition-colors {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300' }}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                @endif

                {{-- Label --}}
                <span x-show="!sidebarCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap flex-1 transition-opacity duration-200">
                    {{ $item['label'] }}
                </span>

                {{-- Active Indicator --}}
                @if ($isActive)
                    <span x-show="!sidebarCollapsed"
                        class="w-1.5 h-1.5 bg-indigo-600 rounded-full ml-2"></span>
                @endif
            </a>

            {{-- Tooltip (Only visible when collapsed) --}}
            <div x-show="sidebarCollapsed"
                x-cloak
                class="absolute left-14 top-1.5 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap pointer-events-none shadow-lg">
                {{ $item['label'] }}
            </div>
        </div>


        {{-- DROPDOWN --}}
    @elseif ($item['type'] === 'dropdown')
        <div x-data="{ open: {{ $hasActiveChild ? 'true' : 'false' }} }"
            class="relative group mb-1">

            <button
                @click="sidebarCollapsed ? (window.dispatchEvent(new CustomEvent('sidebar-toggle', {detail: false}))) : (open = !open)"
                class="flex items-center w-full p-2 rounded-lg transition-colors duration-200 ease-in-out w-full
                           {{ $isActive
                               ? 'bg-gray-50 text-indigo-600 dark:bg-gray-800/50 dark:text-indigo-300'
                               : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}"
                :class="sidebarCollapsed ? 'justify-center px-2' : '{{ $paddingClass }}'">

                {{-- Icon --}}
                <svg class="w-5 h-5 shrink-0 {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500' }}"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>

                {{-- Label --}}
                <span x-show="!sidebarCollapsed"
                    class="ml-3 text-sm font-medium whitespace-nowrap flex-1 text-left transition-opacity duration-200">
                    {{ $item['label'] }}
                </span>

                {{-- Chevron --}}
                <div x-show="!sidebarCollapsed">
                    <svg class="w-4 h-4 transition-transform duration-200"
                        :class="{ 'rotate-90': open }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </button>

            {{-- Tooltip for Parent (Collapsed) --}}
            <div x-show="sidebarCollapsed"
                x-cloak
                class="absolute left-14 top-1.5 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap pointer-events-none shadow-lg">
                {{ $item['label'] }}
            </div>

            {{-- Children Container --}}
            <div x-show="!sidebarCollapsed && open"
                x-collapse
                class="overflow-hidden space-y-0.5 mt-1">
                @foreach ($item['children'] as $child)
                    {{-- Recursive call: No need to pass collapsed prop --}}
                    <x-sidebar-menu-item :item="$child"
                        :level="$level + 1" />
                @endforeach
            </div>
        </div>
    @endif
@endif
