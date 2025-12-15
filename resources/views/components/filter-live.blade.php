@props(['filters', 'uri' => null, 'createBtn' => null])

{{-- 
    State Management:
    x-data="{ expanded: true }" -> Defaults to open.
--}}
<div x-data="{ expanded: true }"
    {{ $attributes->merge(['class' => 'w-full bg-white dark:bg-gray-800 rounded-lg shadow mb-6 overflow-hidden transition-all duration-300']) }}>

    {{-- HEADER / TOGGLE BAR --}}
    <div class="px-6 py-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600 flex justify-between items-center cursor-pointer select-none hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
        @click="expanded = !expanded">

        <div class="flex items-center space-x-2">
            {{-- Animated Chevron Icon --}}
            <svg class="w-5 h-5 text-gray-500 transition-transform duration-300"
                :class="{ 'rotate-180': expanded, 'rotate-0': !expanded }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"></path>
            </svg>
            <h3 class="font-semibold text-gray-700 dark:text-gray-200">
                {{ __('Filter Options') }}
            </h3>
        </div>

        {{-- SMART CREATE BUTTON (Visible when Minimized) --}}
        <div x-show="!expanded"
            x-cloak
            class="transition-opacity duration-200">
            @if ($uri && $createBtn)
                <a href="{{ route($uri) }}"
                    @click.stop
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                    {{ $createBtn }}
                </a>
            @endif
        </div>
    </div>

    {{-- EXPANDABLE CONTENT AREA --}}
    <div x-show="expanded"
        x-collapse
        class="p-6">

        {{-- No <form> tag needed for Livewire --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($filters as $filter)
                <div>
                    <x-input-label :for="$filter['name']"
                        :value="$filter['label']" />

                    {{-- TEXT INPUT --}}
                    @if (($filter['type'] ?? 'text') === 'text')
                        <x-text-input id="{{ $filter['name'] }}"
                            {{-- Livewire Binding with Debounce --}}
                            wire:model.live.debounce.300ms="{{ $filter['name'] }}"
                            type="text"
                            class="mt-1 block w-full"
                            :placeholder="$filter['placeholder'] ?? ''" />

                        {{-- SELECT INPUT --}}
                    @elseif (($filter['type'] ?? '') === 'select')
                        <select id="{{ $filter['name'] }}"
                            {{-- Livewire Binding --}}
                            wire:model.live="{{ $filter['name'] }}"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">

                            <option value="">{{ $filter['defaultOptionText'] ?? 'Select' }}</option>

                            @foreach ($filter['options'] ?? [] as $option)
                                @php
                                    $val = is_array($option) ? $option['value'] : $option;
                                    $txt = is_array($option) ? $option['text'] : ucfirst($option);
                                @endphp
                                <option value="{{ $val }}">
                                    {{ $txt }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- FOOTER ACTIONS --}}
        <div
            class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center space-x-3">

                {{-- Reset Button (Livewire Action) --}}
                <button wire:click="resetFilters"
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                    {{ __('Reset') }}
                </button>

                {{-- Optional: Loading Indicator --}}
                <span wire:loading
                    class="text-sm text-gray-500 ml-2">
                    Loading...
                </span>
            </div>

            {{-- Create Button (Footer version) --}}
            @if ($uri && $createBtn)
                <a href="{{ route($uri) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ $createBtn }}
                </a>
            @endif
        </div>
    </div>
</div>
