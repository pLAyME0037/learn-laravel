@props(['action', 'filters', 'uri' => null, 'createBtn' => null])

{{-- 
    State Management:
    x-data="{ expanded: true }" -> Defaults to open. Change to false if you want it closed by default.
--}}
<div x-data="{ expanded: true }"
    {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow mb-6 overflow-hidden transition-all duration-300']) }}>

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

        {{-- 
            SMART CREATE BUTTON:
            Only visible here when the box is MINIMIZED.
        --}}
        <div x-show="!expanded"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            @if ($uri && $createBtn)
                <a href="{{ route($uri) }}"
                    @click.stop
                    {{-- Prevent toggling the box when clicking the button --}}
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                    {{ $createBtn }}
                </a>
            @endif
        </div>
    </div>

    {{-- EXPANDABLE CONTENT AREA --}}
    <div x-show="expanded"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="p-6">

        <form method="GET"
            action="{{ $action }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($filters as $filter)
                    <div>
                        <x-input-label :for="$filter['name']"
                            :value="$filter['label']" />

                        {{-- TEXT INPUT --}}
                        @if (($filter['type'] ?? 'text') === 'text')
                            <x-text-input :id="$filter['name']"
                                :name="$filter['name']"
                                type="text"
                                value="{{ $filter['value'] ?? '' }}"
                                class="mt-1 block w-full"
                                :placeholder="$filter['placeholder'] ?? ''" />

                            {{-- SELECT INPUT --}}
                        @elseif (($filter['type'] ?? '') === 'select')
                            <select id="{{ $filter['name'] }}"
                                name="{{ $filter['name'] }}"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                {{-- Alpine Bindings --}}
                                @if (!empty($filter['alpine_model'])) x-model="{{ $filter['alpine_model'] }}" @endif
                                @if (!empty($filter['onChange'])) @change="{{ $filter['onChange'] }}" @endif>
                                <option value="">{{ $filter['defaultOptionText'] ?? 'Select' }}</option>

                                {{-- Dynamic Alpine Options --}}
                                @if (!empty($filter['alpine_options']))
                                    <template x-for="item in {{ $filter['alpine_options'] }}"
                                        :key="item.{{ $filter['option_value'] ?? 'id' }}">
                                        <option :value="item.{{ $filter['option_value'] ?? 'id' }}"
                                            x-text="item.{{ $filter['option_text'] ?? 'name' }}"
                                            :selected="item.{{ $filter['option_value'] ?? 'id' }} ==
                                                {{ $filter['alpine_model'] ?? 'null' }}">
                                        </option>
                                    </template>
                                    {{-- Static PHP Options --}}
                                @else
                                    @foreach ($filter['options'] ?? [] as $option)
                                        @php
                                            $val = is_array($option) ? $option['value'] : $option;
                                            $txt = is_array($option) ? $option['text'] : ucfirst($option);
                                            $isSelected = ($filter['selectedValue'] ?? '') == $val;
                                        @endphp
                                        <option value="{{ $val }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                            {{ $txt }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        @endif

                        {{-- Error Handling --}}
                        @if (isset($filter['errorMessages']))
                            @php
                                $msg = is_array($filter['errorMessages'])
                                    ? $filter['errorMessages'][0] ?? ''
                                    : $filter['errorMessages'];
                            @endphp
                            @if ($msg)
                                <p class="text-red-500 text-xs mt-1">{{ $msg }}</p>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- FOOTER ACTIONS (Visible when Expanded) --}}
            <div
                class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-4">
                <div class="flex items-center space-x-3">
                    <x-primary-button>
                        {{ __('Apply Filters') }}
                    </x-primary-button>

                    <a href="{{ $action }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                        {{ __('Reset') }}
                    </a>
                </div>

                {{-- Create Button (Footer version) --}}
                @if ($uri && $createBtn)
                    <a href="{{ route($uri) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ $createBtn }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
