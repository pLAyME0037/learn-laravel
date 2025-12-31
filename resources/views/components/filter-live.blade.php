@props([
    'filters' => [],
    'uri' => null,
    'createBtn' => null,
    'threshold' => 10, // If options count <= this, use native <select>
])

<div x-data="{ expanded: true }"
    {{ $attributes->merge(['class' => 'w-full bg-white dark:bg-gray-800 rounded-lg shadow mb-6 transition-all duration-300']) }}>

    <!-- HEADER / TOGGLE BAR -->
    <div class="px-6 py-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600 flex justify-between items-center cursor-pointer select-none hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
        @click="expanded = !expanded">

        <div class="flex items-center space-x-2">
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

        <!-- Create button when collapsed -->
        <div x-show="!expanded"
            x-cloak
            class="transition-opacity duration-200">
            @if ($uri && $createBtn)
                <a href="{{ route($uri) }}"
                    @click.stop
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    {{ $createBtn }}
                </a>
            @endif
        </div>
    </div>

    <!-- EXPANDABLE CONTENT -->
    <div x-show="expanded"
        x-collapse
        class="p-6">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($filters as $filter)
                <div>
                    <x-input-label :for="$filter['name']"
                        :value="$filter['label']" />

                    @php
                        $type = $filter['type'] ?? 'text';
                        $name = $filter['name'];
                        $options = $filter['options'] ?? [];
                        $hasOptions = is_array($options) && count($options) > 0;
                        $useAutocomplete = $type === 'select' && $hasOptions && count($options) > $threshold;
                    @endphp

                    <!-- TEXT INPUT -->
                    @if ($type === 'text')
                        <x-text-input id="{{ $name }}"
                            wire:model.live.debounce.300ms="{{ $name }}"
                            type="text"
                            class="mt-1 block w-full"
                            :placeholder="$filter['placeholder'] ?? ''" />

                        <!-- SELECT INPUT -->
                    @elseif ($type === 'select')
                        @if ($useAutocomplete)
                            <!-- Autocomplete for large lists -->
                            <x-autocomplete-select :items="$options"
                                wire-model="{{ $name }}"
                                placeholder="{{ $filter['defaultOptionText'] ?? 'Search or select...' }}" />
                        @else
                            <!-- Native select for small lists or no options -->
                            <select id="{{ $name }}"
                                wire:model.live="{{ $name }}"
                                wire:key="select-{{ $name }}-{{ $hasOptions ? count($options) : 0 }}"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ $filter['defaultOptionText'] ?? 'All' }}</option>

                                @foreach ($options as $option)
                                    @php
                                        $val = is_array($option) ? $option['id'] ?? ($option['value'] ?? '') : $option;
                                        $txt = is_array($option)
                                            ? $option['label'] ?? ($option['text'] ?? $val)
                                            : ucfirst($option);
                                        $sub = $option['sub'] ?? null;
                                    @endphp
                                    <option value="{{ $val }}">
                                        {{ $txt }}
                                        @if ($sub)
                                            — {{ $sub }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

        <!-- FOOTER ACTIONS -->
        <div
            class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <button wire:click="resetFilters"
                    type="button"
                    class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    {{ __('Reset') }}
                </button>

                <span wire:loading
                    class="text-sm text-gray-500">Loading...</span>
            </div>

            @if ($uri && $createBtn)
                <a href="{{ route($uri) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    {{ $createBtn }}
                </a>
            @endif
        </div>
    </div>
</div>
