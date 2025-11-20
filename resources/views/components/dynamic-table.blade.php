@props(['headers', 'data', 'actions' => [], 'options' => []])

{{-- Main container for the table, applying basic styling and merging custom wrapper classes --}}
<div
    {{ $attributes->merge([
        'class' => 'bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg ' . ($options['wrapperClass'] ?? ''),
    ]) }}>
    {{-- Wrapper for horizontal scrolling on smaller screens --}}
    <div class="overflow-x-auto">
        {{-- The main table element, applying full width and merging custom table classes --}}
        <table class="w-full table-auto {{ $options['tableClass'] ?? '' }}">
            {{-- Table header section --}}
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr class="bg-gray-50 dark:bg-gray-700">
                    {{-- Loop through the provided headers to create table columns --}}
                    @foreach ($headers as $key => $label)
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{-- Display the header label --}}
                            {{ $label }}
                        </th>
                    @endforeach
                    {{-- If actions are defined, add an extra column for them --}}
                    @if (count($actions) > 0)
                        <th scope="col"
                            class="relative px-6 py-3">
                            {{-- Screen reader only text for accessibility --}}
                            <span class="sr-only">Actions</span>
                        </th>
                    @endif
                </tr>
            </thead>
            {{-- Table body section --}}
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                {{-- Loop through each row of data to populate the table --}}
                @foreach ($data as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{-- Loop through headers again to display data for each column in the current row --}}
                        @foreach ($headers as $key => $label)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{-- Display the data for the current key, or an empty string if not found --}}
                                {{ $row[$key] ?? '' }}
                            </td>
                        @endforeach
                        {{-- If actions are defined, render them in the last column --}}
                        @if (count($actions) > 0)
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                {{-- Loop through each action defined --}}
                                @foreach ($actions as $action)
                                    {{-- Check if a condition exists for the action and if it evaluates to false, skip this action --}}
                                    @if (isset($action['condition']) && !$action['condition']($row))
                                        @continue
                                    @endif
                                    <span class="inline-flex"> {{-- Wrap each action in a span for better isolation --}}
                                        {{-- If the action is a standard link --}}
                                        @if (isset($action['route']))
                                            @php
                                                $routeParams = [];
                                                if (isset($action['params']) && is_array($action['params'])) {
                                                    foreach ($action['params'] as $paramKey => $dataKey) {
                                                        $routeParams[$paramKey] = data_get($row, $dataKey);
                                                    }
                                                }
                                            @endphp
                                            <a href="{{ route($action['route'], $routeParams) }}"
                                                class="{{ $action['class'] ?? 'text-indigo-600 hover:text-indigo-900' }}">
                                                {{ $action['label'] }}
                                            </a>
                                            {{-- If the action is a delete form --}}
                                        @elseif (isset($action['method']) && $action['method'] === 'DELETE')
                                            @php
                                                $routeParams = [];
                                                if (isset($action['params']) && is_array($action['params'])) {
                                                    foreach ($action['params'] as $paramKey => $dataKey) {
                                                        $routeParams[$paramKey] = data_get($row, $dataKey);
                                                    }
                                                }
                                            @endphp
                                            <form action="{{ route($action['route'], $routeParams) }}"
                                                method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="{{ $action['class'] ?? 'text-red-600 hover:text-red-900' }}"
                                                    onclick="return confirm('Are you sure you want to delete this item?');">
                                                    {{ $action['label'] }}
                                                </button>
                                            </form>
                                        @else
                                            {{-- Fallback for actions without a route or delete method --}}
                                            <span class="{{ $action['class'] ?? '' }}">
                                                {{ $action['label'] }}
                                            </span>
                                        @endif
                                    </span>
                                @endforeach
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
