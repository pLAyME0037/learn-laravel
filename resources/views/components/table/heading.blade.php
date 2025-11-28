@props([
    'sortable' => null,
    'direction' => null,
    'multiColumn' => false,
    'align' => 'left',
    'width' => '',
])

<th scope="col"
    {{ $attributes->merge(['class' => 'px-1 py-3 bg-gray-50 dark:bg-gray-700 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider']) }}
    style="text-align: {{ $align }};">

    @unless ($sortable)
        <span>{{ $slot }}</span>
    @else
        <a href="{{ request()->fullUrlWithQuery(['sort' => $sortable, 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}"
            class="group inline-flex items-center space-x-1 cursor-pointer hover:text-gray-700 dark:hover:text-gray-200">
            <span>{{ $slot }}</span>

            <span class="relative flex items-center">
                @if ($direction === 'asc')
                    <svg class="w-3 h-3 text-indigo-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 15l7-7 7 7">
                        </path>
                    </svg>
                @elseif ($direction === 'desc')
                    <svg class="w-3 h-3 text-indigo-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                @else
                    {{-- Neutral Sort Icon --}}
                    <svg class="w-3 h-3 text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4">
                        </path>
                    </svg>
                @endif
            </span>
        </a>
    @endunless
</th>
