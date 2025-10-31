@props(['item', 'collapsed'])

@php
    use Illuminate\Support\Facades\Gate;
    $hasPermission = true;
    if (isset($item['can'])) {
        if (is_array($item['can'])) {
            $hasPermission = Gate::any($item['can']);
        } else {
            $hasPermission = Gate::allows($item['can']);
        }
    }
@endphp

@if ($hasPermission)
    @if ($item['type'] === 'heading')
        <div x-show="!collapsed"
            class="px-6 py-2 {{ $item['label'] === 'Admin' ? 'mt-0' : '' }} dark:bg-blue-900/20 border-r-4 bg-blue-50">
            <h2 class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">
                {{ $item['label'] }}
            </h2>
        </div>
    @elseif ($item['type'] === 'link')
        <li>
            <a href="{{ route($item['route']) }}"
                class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                :class="collapsed ? 'justify-center' : ''">
                <svg class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span x-show="!collapsed"
                    class="mx-3">
                    {{ $item['label'] }}
                </span>
            </a>
        </li>
    @elseif ($item['type'] === 'dropdown')
        <li x-data="{ open: false }">
            <button @click="open = !open"
                class="w-full flex items-center px-6 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                :class="collapsed ? 'justify-center' : ''">
                <svg class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <template x-if="!collapsed">
                    <span class="mx-3 text-left flex-1">
                        {{ $item['label'] }}
                    </span>
                </template>
                <template x-if="!collapsed">
                    <svg :class="{ 'rotate-180': open }"
                        class="w-4 h-4 transition-transform"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </template>
            </button>

            <div x-show="!collapsed && open"
                x-collapse
                class="bg-gray-50 dark:bg-gray-700">
                @foreach ($item['children'] as $child)
                    @php
                        $childHasPermission = true;
                        if (isset($child['can'])) {
                            if (is_array($child['can'])) {
                                $childHasPermission = Gate::any($child['can']);
                            } else {
                                $childHasPermission = Gate::allows($child['can']);
                            }
                        }
                    @endphp

                    @if ($childHasPermission)
                        <a href="{{ route($child['route']) }}"
                            class="flex items-center px-6 py-2 pl-14 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                            {{ $child['label'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </li>
    @endif
@endif
