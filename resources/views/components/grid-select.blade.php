@props([
    'name',
    'label' => 'Select Option',
    'options' => [],
    'selected' => null,
    'columns' => [],
    'placeholder' => 'Select...',
    'disabled' => false,
])

<div class="mt-4 relative"
    x-data="{
        open: false,
        dropUp: false,
        selectedId: @entangle($attributes->wire('model')),
        selectedLabel: '{{ $placeholder }}',
        items: @js($options),
        isDisabled: @js($disabled),
        focusedIndex: -1, // For keyboard navigation
    
        init() {
            this.$watch('items', () => this.updateLabel());
            this.$watch('selectedId', () => this.updateLabel());
    
            // Initial Label Set
            this.updateLabel();
    
            // Smart Positioning Logic
            this.$watch('open', (value) => {
                if (value) {
                    this.$nextTick(() => {
                        const trigger = this.$refs.trigger.getBoundingClientRect();
                        const spaceBelow = window.innerHeight - trigger.bottom;
                        // If less than 250px below, flip up
                        this.dropUp = spaceBelow < 250;
    
                        // Focus first item if opening via keyboard
                        if (this.focusedIndex === -1 && this.items.length > 0) {
                            this.focusedIndex = 0;
                        }
                    });
                } else {
                    this.focusedIndex = -1; // Reset focus
                }
            });
        },
    
        updateLabel() {
            if (!this.selectedId) {
                this.selectedLabel = '{{ $placeholder }}';
                return;
            }
            // Strict equality check (ensure IDs are strings or ints consistently)
            const found = this.items.find(i => String(i.id) === String(this.selectedId));
            this.selectedLabel = found ? found.display_label : '{{ $placeholder }}';
        },
    
        toggle() {
            if (this.isDisabled) return;
            this.open = !this.open;
        },
    
        selectOption(item) {
            this.selectedId = item.id;
            this.open = false;
        },
    
        // Keyboard Navigation Handlers
        navigate(direction) {
            if (!this.open) {
                this.open = true;
                return;
            }
    
            if (direction === 'down') {
                this.focusedIndex = (this.focusedIndex + 1) % this.items.length;
            } else if (direction === 'up') {
                this.focusedIndex = (this.focusedIndex - 1 + this.items.length) % this.items.length;
            }
    
            // Scroll into view logic could go here
        },
    
        selectFocused() {
            if (this.open && this.focusedIndex >= 0 && this.items[this.focusedIndex]) {
                this.selectOption(this.items[this.focusedIndex]);
            }
        }
    }"
    x-effect="items = @js($options); isDisabled = @js($disabled);"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="w-full relative">

    <x-input-label :value="$label"
        class="mb-1 block" />

    {{-- TRIGGER BOX --}}
    <button type="button"
        x-ref="trigger"
        @click="toggle()"
        @keydown.arrow-down.prevent="navigate('down')"
        @keydown.arrow-up.prevent="navigate('up')"
        @keydown.enter.prevent="selectFocused()"
        @keydown.space.prevent="toggle()"
        aria-haspopup="listbox"
        :aria-expanded="open"
        :disabled="isDisabled"
        class="relative w-full border rounded-md shadow-sm py-2.5 px-3 text-left transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        :class="isDisabled ? 'bg-gray-100 cursor-not-allowed border-gray-200 text-gray-400' :
            'cursor-pointer bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 hover:border-indigo-400'">

        <span class="block truncate font-mono text-xs md:text-sm"
            :class="isDisabled ? 'text-gray-400' : 'text-gray-700 dark:text-gray-300'"
            x-text="selectedLabel"></span>

        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="h-5 w-5 text-gray-400 transition-transform duration-200"
                :class="{ 'rotate-180': open }"
                viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    {{-- DROPDOWN LIST --}}
    <div x-show="open && !isDisabled"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full min-w-[320px] bg-white dark:bg-gray-800 shadow-xl rounded-md ring-1 ring-black ring-opacity-5 max-h-60 overflow-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600"
        :class="dropUp ? 'bottom-full mb-1' : 'top-full mt-1'"
        role="listbox"
        style="display: none;">

        {{-- HEADER --}}
        <div
            class="grid grid-cols-12 gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 font-bold text-xs uppercase text-gray-600 dark:text-gray-300 sticky top-0 z-10">
            @foreach ($columns as $col)
                <div class="col-span-{{ $col['span'] ?? 12 }} {{ $col['align'] ?? 'text-left' }} px-2">
                    {{ $col['header'] }}
                </div>
            @endforeach
        </div>

        {{-- BODY --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <template x-for="(item, index) in items"
                :key="item.id">
                <div @click="selectOption(item)"
                    role="option"
                    :aria-selected="selectedId == item.id"
                    :class="{
                        'bg-indigo-50 dark:bg-indigo-900/50': selectedId == item.id,
                        'bg-gray-100 dark:bg-gray-700': focusedIndex === index
                    }"
                    class="cursor-pointer select-none relative py-2.5 px-4 hover:bg-indigo-600 hover:text-white group transition-colors">

                    <div class="grid grid-cols-12 gap-2 items-center text-xs md:text-sm">
                        @foreach ($columns as $col)
                            <div class="col-span-{{ $col['span'] ?? 12 }} {{ $col['align'] ?? 'text-left' }} truncate {{ $col['class'] ?? '' }} px-2"
                                :class="{ 'text-gray-500 dark:text-gray-400 group-hover:text-indigo-200': {{ $loop->index > 0 ? 'true' : 'false' }} }"
                                x-text="item.{{ $col['key'] }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </template>

            <template x-if="items.length === 0">
                <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-xs italic">
                    No options available.
                </div>
            </template>
        </div>
    </div>
</div>
