# Filter Component Plan (01/11/2025)

## 1. Objective
To refactor the existing filter section in `resources/views/admin/users/index.blade.php` into a reusable Blade component (`x-filter-box`). This component will be flexible, easy to maintain, and include quality-of-life features such as minimizing/maximizing the filter box and preserving selected options across pagination.

## 2. Component Design (`x-filter-box`)

### 2.1. Component Name
`x-filter-box`

### 2.2. Props
The component will accept the following props to ensure flexibility:

*   `action`: The form action URL (e.g., `route('admin.users.index')`).
*   `filters`: An array of filter configurations. Each configuration will be an associative array defining a single filter input.
*   `searchPlaceholder`: (Optional) Placeholder text for the search input.
*   `initialState`: (Optional) 'maximized' or 'minimized' for the filter box. Defaults to 'maximized'.

### 2.3. Filter Configuration Structure (`filters` prop)
Each item in the `filters` array will have a `type` and other properties based on the type:

#### a. Text Input (`type: 'text'`)
*   `name`: Input field name (e.g., 'search').
*   `label`: Label for the input (e.g., 'Search').
*   `value`: Current value (e.g., `$search`).
*   `placeholder`: (Optional) Placeholder text.

#### b. Select Input (`type: 'select'`)
*   `name`: Select field name (e.g., 'role', 'status').
*   `label`: Label for the select (e.g., 'Role', 'Status').
*   `options`: An array of options. Each option can be:
    *   A simple string (value and text are the same).
    *   An associative array `['value' => '...', 'text' => '...']`.
*   `selectedValue`: The currently selected value (e.g., `$selectedRole`, `$status`).
*   `defaultOptionText`: (Optional) Text for the "All" option (e.g., 'All Roles'). Defaults to 'All'.
*   `errorMessages`: (Optional) Error messages from validation (e.g., `$errors->get('role')`).

#### c. Radio/Checkbox (Future consideration, not in initial scope but designed for flexibility)
*   `type`: 'radio' or 'checkbox'.
*   `name`: Input field name.
*   `label`: Group label.
*   `options`: Array of `['value' => '...', 'text' => '...']`.
*   `selectedValues`: Array of currently selected values.

### 2.4. Quality of Life Features

#### a. Minimize/Maximize Filter Box Layout
*   Use Alpine.js to toggle the visibility of the filter inputs.
*   A button will be provided to switch between minimized (only showing a title/summary) and maximized (showing all filter inputs).
*   The state (minimized/maximized) will be managed by Alpine.js.

#### b. Save Selected Option When Navigating Pagination
*   This is inherently handled by the `GET` form submission. The component will receive the current filter values via props, which are typically passed from the controller based on the request query parameters. When the form is submitted or pagination links are clicked, the query parameters will persist.

## 3. Implementation Steps

### Step 3.1: Create `resources/views/components/filter-box.blade.php`
*   Create the new Blade component file.
*   Copy the existing filter form structure into this new component.
*   Replace hardcoded filter inputs with dynamic rendering based on the `filters` prop.
*   Add Alpine.js for the minimize/maximize functionality.

### Step 3.2: Update `resources/views/admin/users/index.blade.php`
*   Remove the existing filter section.
*   Integrate the `x-filter-box` component, passing the necessary `action` and `filters` props.
*   Prepare the `filters` array in the `index.blade.php` to pass to the component.

### Step 3.3: (Optional) Create a dedicated Livewire component for more complex filter logic
*   For very complex filtering scenarios, a Livewire component might be more suitable. However, for the current requirements, a Blade component with Alpine.js for UI enhancements is sufficient and simpler. This will be a future consideration if the filtering logic grows significantly.

## 4. Detailed Component Structure (Conceptual)

```blade
<!-- resources/views/components/filter-box.blade.php -->
<div x-data="{ minimized: {{ $initialState === 'minimized' ? 'true' : 'false' }} }"
    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Filters') }}</h3>
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
                                <option value="">{{ $filter['defaultOptionText'] ?? 'All' }}</option>
                                @foreach ($filter['options'] as $option)
                                    @php
                                        $optionValue = is_array($option) ? $option['value'] : $option;
                                        $optionText = is_array($option) ? $option['text'] : ucfirst($option);
                                    @endphp
                                    <option value="{{ $optionValue }}"
                                        {{ (string) old($filter['name'], $filter['selectedValue']) === (string) $optionValue ? 'selected' : '' }}>
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
                    {{-- Add more filter types here (radio, checkbox) if needed in the future --}}
                @endforeach

                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <x-primary-button type="submit">
                        {{ __('Filter') }}
                    </x-primary-button>
                    <a href="{{ $action }}"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        {{ __('Reset') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
```

## 5. Testing Strategy
*   Verify search functionality.
*   Verify role filter functionality.
*   Verify status filter functionality.
*   Test minimize/maximize toggle.
*   Test pagination with filters applied.
*   Check for any validation errors.

## 6. Future Enhancements
*   Add support for radio button filters.
*   Add support for checkbox filters.
*   Implement client-side filter persistence (e.g., using local storage) if server-side persistence is not sufficient.
