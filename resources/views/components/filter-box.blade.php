@props(['action', 'filters', 'uri' => null, 'createBtn' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-6']) }}>
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
                            {{-- 1. Alpine Model Binding --}}
                            @if (!empty($filter['alpine_model'])) x-model="{{ $filter['alpine_model'] }}" @endif
                            {{-- 2. Alpine Change Event --}}
                            @if (!empty($filter['onChange'])) @change="{{ $filter['onChange'] }}" @endif>
                            <option value="">{{ $filter['defaultOptionText'] ?? 'Select' }}</option>

                            {{-- A. DYNAMIC ALPINE OPTIONS --}}
                            @if (!empty($filter['alpine_options']))
                                <template x-for="item in {{ $filter['alpine_options'] }}"
                                    :key="item.{{ $filter['option_value'] ?? 'id' }}">
                                    <option :value="item.{{ $filter['option_value'] ?? 'id' }}"
                                        x-text="item.{{ $filter['option_text'] ?? 'name' }}"
                                        :selected="item.{{ $filter['option_value'] ?? 'id' }} ==
                                            {{ $filter['alpine_model'] ?? 'null' }}">
                                    </option>
                                </template>

                                {{-- B. STATIC PHP OPTIONS --}}
                            @else
                                {{-- FIX: Added '?? []' to prevent crash if options key is missing --}}
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

                    {{-- Error Message --}}
                    @if (isset($filter['errorMessages']) && is_array($filter['errorMessages']) && count($filter['errorMessages']) > 0)
                        <p class="text-red-500 text-xs mt-1">
                            {{ $filter['errorMessages'][0] }}
                        </p>
                    @elseif (isset($filter['errorMessages']) && is_string($filter['errorMessages']))
                        <p class="text-red-500 text-xs mt-1">
                            {{ $filter['errorMessages'] }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <a href="{{ $action }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Reset Filters') }}
                </a>

                <x-primary-button>
                    {{ __('Filter Results') }}
                </x-primary-button>
            </div>

            @if ($uri && $createBtn)
                <a href="{{ route($uri) }}"
                    class="ml-2 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ $createBtn }}
                </a>
            @endif
        </div>
    </form>
</div>
