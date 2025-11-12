<!-- resources/views/components/filter-box.blade.php -->
@props(['action', 'filters', 'initialState' => 'maximized', 'uri' => null, 'button' => null])

<div x-data="{ minimized: {{ $initialState === 'minimized' ? 'true' : 'false' }} }"
    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
    <div class="p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ __('Filters') }}
            </h3>
            <button @click="minimized = !minimized"
                class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <span x-show="minimized">{{ __('Maximize') }}</span>
                <span x-show="!minimized">{{ __('Minimize') }}</span>
            </button>
        </div>

        <form method="GET"
            action="{{ $action }}"
            class="space-y-4"
            x-show="!minimized">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach ($filters as $filter)
                    {{-- Ensure $filter is an array and has the 'type' key before proceeding --}}
                    @if (is_array($filter) && array_key_exists('type', $filter))
                        @if ($filter['type'] === 'text')
                            <div>
                                <x-input-label for="{{ $filter['name'] }}"
                                    :value="__($filter['label'])" />
                                <x-text-input id="{{ $filter['name'] }}"
                                    name="{{ $filter['name'] }}"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :value="old($filter['name'], $filter['value'])"
                                    placeholder="{{ $filter['placeholder'] ?? '' }}" />
                            </div>
                        @elseif ($filter['type'] === 'select')
                            <div>
                                <x-input-label for="{{ $filter['name'] }}"
                                    :value="__($filter['label'])" />
                                <select id="{{ $filter['name'] }}"
                                    name="{{ $filter['name'] }}"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    @php
                                        // Safely access defaultOptionText, providing a default if it's not an array or doesn't exist
                                        $defaultOptionText = is_array($filter['defaultOptionText'] ?? null)
                                            ? $filter['defaultOptionText']['text'] ?? 'All'
                                            : $filter['defaultOptionText'] ?? 'All';
                                    @endphp
                                    <option value="">{{ $defaultOptionText }}</option>
                                    @foreach ($filter['options'] as $option)
                                        @php
                                            // Safely access option value and text
                                            $optionValue = is_array($option) ? $option['value'] ?? $option : $option;
                                            $optionText = is_array($option)
                                                ? $option['text'] ?? ucfirst($option)
                                                : ucfirst($option);
                                        @endphp
                                        <option value="{{ $optionValue }}"
                                            {{ (string) old($filter['name'], $filter['selectedValue'] ?? null) === (string) $optionValue ? 'selected' : '' }}>
                                            {{ $optionText }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (isset($filter['errorMessages']))
                                    <x-input-error class="mt-2"
                                        :messages="$filter['errorMessages']" />
                                @endif
                            </div>
                        @endif
                    @endif
                    {{-- Add more filter types here (radio, checkbox) if needed in the future --}}
                @endforeach

                <!-- Actions -->
                <div class="md:col-span-3 flex items-end space-x-2">
                    <x-primary-button type="submit">
                        {{ __('Filter') }}
                    </x-primary-button>
                    <a href="{{ $action }}"
                        class="px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-900 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-400 focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('Reset') }}
                    </a>
                </div>
                @if ($uri && $button && is_string($uri) && $uri !== '')
                    <div class="md:col-span-1 flex justify-end">
                        <a href="{{ route($uri) }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __($button) }}
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
