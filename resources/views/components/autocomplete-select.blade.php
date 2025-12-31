@props([
    'items' => [],
    'placeholder' => 'Select Option',
    'wireModel' => null,
])

<div x-data="{
    open: false,
    search: '',
    selectedId: null,
    selectedLabel: '',
    items: @js($items),

    get filteredItems() {
        if (this.search === '') return this.items;
        return this.items.filter(item =>
            item.label.toLowerCase().includes(this.search.toLowerCase()) ||
            (item.sub && item.sub.toLowerCase().includes(this.search.toLowerCase()))
        );
    },

    selectItem(id, label) {
        this.selectedId = id;
        this.selectedLabel = label;
        this.search = label;
        this.open = false;

        // Directly update Livewire property (immediate sync, no defer)
        $wire.set('{{ $wireModel }}', id);
    },

    clearSelection() {
        this.selectedId = null;
        this.selectedLabel = '';
        this.search = '';
        this.open = true;

        $wire.set('{{ $wireModel }}', null);
    },

    init() {
        // Pull initial value from Livewire
        this.selectedId = $wire.get('{{ $wireModel }}');

        if (this.selectedId) {
            const found = this.items.find(i => i.id == this.selectedId);
            if (found) {
                this.selectedLabel = found.label;
                this.search = found.label;
            }
        }

        // Keep in sync if Livewire changes the value externally (e.g., reset)
        this.$watch('$wire.{{ $wireModel }}', value => {
            this.selectedId = value;
            if (value) {
                const found = this.items.find(i => i.id == value);
                if (found) {
                    this.selectedLabel = found.label;
                    this.search = found.label;
                }
            } else {
                this.search = '';
            }
        });
    }
}"
    class="relative w-full">

    <div class="relative">
        <input type="text"
            x-model="search"
            @focus="open = true"
            @click="selectedId ? clearSelection() : open = true"
            @click.away="open = false"
            @keydown.escape="open = false"
            placeholder="{{ $placeholder }}"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">

        <!-- Clear (X) when selected -->
        <div class="absolute inset-y-0 right-0 flex items-center pr-2">
            <button x-show="selectedId"
                @click.stop="clearSelection()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                <svg class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Chevron when nothing selected -->
            <svg x-show="!selectedId"
                class="h-5 w-5 text-gray-400 pointer-events-none"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <!-- Dropdown -->
    <div x-show="open"
        x-transition.opacity.duration.200ms
        class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm"
        style="display: none;">

        <template x-for="item in filteredItems"
            :key="item.id">
            <div @click="selectItem(item.id, item.label)"
                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-600/50 hover:text-white text-gray-900 dark:text-gray-200"
                :class="{ 'bg-indigo-600 text-white': selectedId == item.id }">

                <span x-text="item.label"
                    class="block truncate font-medium"></span>
                <span x-show="item.sub"
                    x-text="item.sub"
                    class="block text-xs text-gray-500 dark:text-gray-400 mt-1"></span>

                <span x-show="selectedId == item.id"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-white">
                    <svg class="h-5 w-5"
                        fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            </div>
        </template>

        <div x-show="filteredItems.length === 0"
            class="py-2 pl-3 pr-9 text-gray-500 text-sm italic">
            No results found.
        </div>
    </div>
</div>
