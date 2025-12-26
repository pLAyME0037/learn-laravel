@props([
    'items' => [], // Array of ['id' => 1, 'label' => 'CS101']
    'placeholder' => 'Select Option',
    'wireModel' => null, // The Livewire property to bind (e.g. 'course_id')
    'id' => null, // Unique ID for labels
])

<div x-data="{
    open: false,
    search: '',
    selectedId: @entangle($wireModel),
    items: @js($items),

    get filteredItems() {
        if (this.search === '') return this.items;
        return this.items.filter(item =>
            item.label.toLowerCase().includes(this.search.toLowerCase())
        );
    },

    selectItem(id, label) {
        this.selectedId = id;
        this.search = label; // Display selected name
        this.open = false;
    },

    init() {
        // If there is an initial value (Edit Mode), set the search text
        if (this.selectedId) {
            const found = this.items.find(i => i.id == this.selectedId);
            if (found) this.search = found.label;
        }

        // Watch for external changes (Livewire updates)
        this.$watch('selectedId', value => {
            const found = this.items.find(i => i.id == value);
            this.search = found ? found.label : '';
        });
    }
}"
    class="relative w-full">

    <div class="relative">
        <input type="text"
            x-model="search"
            @focus="open = true"
            @click.away="open = false"
            @keydown.escape="open = false"
            placeholder="{{ $placeholder }}"
            class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 shadow-sm sm:text-sm">
        {{-- Chevron Icon --}}
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    {{-- Dropdown List --}}
    <div x-show="open"
        x-transition.opacity.duration.200ms
        class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        style="display: none;">

        <template x-for="item in filteredItems"
            :key="item.id">
            <div @click="selectItem(item.id, item.label)"
                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white text-gray-900 dark:text-gray-200"
                :class="{ 'bg-indigo-600 text-white': selectedId == item.id }">

                <span x-text="item.label"
                    class="block truncate"
                    :class="{ 'font-semibold': selectedId == item.id, 'font-normal': selectedId != item.id }">
                </span>

                <span x-show="selectedId == item.id"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-white">
                    <svg class="h-5 w-5"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            </div>
        </template>

        {{-- Empty State --}}
        <div x-show="filteredItems.length === 0"
            class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500 text-sm italic">
            No results found.
        </div>
    </div>

</div>
