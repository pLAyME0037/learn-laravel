@props(['group', 'permissions', 'selected' => []])

<div x-data="{
    init() {
            // Optional: Logic to check if all are selected on load to toggle UI states
        },
        selectAll() {
            this.$root.querySelectorAll('.perm-checkbox').forEach(el => el.checked = true);
        },
        deselectAll() {
            this.$root.querySelectorAll('.perm-checkbox').forEach(el => el.checked = false);
        }
}"
    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden mb-6 transition-all hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700">

    {{-- HEADER: Group Name & Actions --}}
    <div
        class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">

        <div class="flex items-center gap-3">
            <div class="p-1.5 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg text-indigo-600 dark:text-indigo-400">
                <svg class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                    {{ $group }}
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ collect($permissions)->count() }} permissions available
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3 text-xs font-medium">
            <button type="button"
                @click="selectAll()"
                class="flex items-center gap-1 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors hover:bg-indigo-50 dark:hover:bg-indigo-900/30 px-2 py-1 rounded">
                <svg class="w-3 h-3"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"></path>
                </svg>
                Select All
            </button>

            <div class="h-4 w-px bg-gray-300 dark:bg-gray-600"></div>

            <button type="button"
                @click="deselectAll()"
                class="flex items-center gap-1 text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-300 transition-colors hover:bg-rose-50 dark:hover:bg-rose-900/30 px-2 py-1 rounded">
                <svg class="w-3 h-3"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Deselect All
            </button>
        </div>
    </div>

    {{-- BODY: Grid of Permissions --}}
    <div class="p-6 bg-white dark:bg-gray-800">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
            @foreach ($permissions as $permission)
                @php
                    // Auto-generate description if missing
                    $desc = $permission->description ?? \Illuminate\Support\Str::headline($permission->name);
                @endphp

                <label
                    class="flex items-start p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors group">
                    {{-- Checkbox --}}
                    <div class="flex items-center h-5 mt-0.5">
                        <input type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->name }}"
                            class="perm-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700 cursor-pointer"
                            {{ in_array($permission->name, $selected) ? 'checked' : '' }}>
                    </div>

                    {{-- Text Content --}}
                    <div class="ml-3">
                        <span
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $permission->name }}
                        </span>
                        <span class="block text-xs text-gray-400 dark:text-gray-500">
                            {{ $desc }}
                        </span>
                    </div>
                </label>
            @endforeach
        </div>
    </div>
</div>
