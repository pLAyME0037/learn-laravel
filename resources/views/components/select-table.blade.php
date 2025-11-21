@props([
    'name', // Input name (e.g., 'class_schedule_id')
    'label' => 'Select Option', // Input Label
    'options' => [], // The JSON data
    'selected' => null, // Currently selected ID
    'columns' => [], // Array defining the grid layout
    'placeholder' => 'Select...',
])

<div class="mt-4 relative"
    x-data="{
        open: false,
        dropUp: false,
        selectedId: '{{ $selected }}',
        selectedLabel: '{{ $placeholder }}',
        items: {{ \Illuminate\Support\Js::from($options) }},
    
        init() {
            // Set Initial Label based on 'display_label' key
            if (this.selectedId) {
                const found = this.items.find(i => i.id == this.selectedId);
                if (found) {
                    this.selectedLabel = found.display_label;
                }
            }
    
            // Smart Positioning Logic
            this.$watch('open', (value) => {
                if (value) {
                    this.$nextTick(() => {
                        const trigger = this.$refs.trigger.getBoundingClientRect();
                        const spaceBelow = window.innerHeight - trigger.bottom;
                        this.dropUp = spaceBelow < 300;
                    });
                }
            });
        },
    
        selectOption(item) {
            this.selectedId = item.id;
            this.selectedLabel = item.display_label;
            this.open = false;
        }
    }"
    @click.outside="open = false">

    <x-input-label :value="$label" />

    {{-- Hidden Input for Form Submission --}}
    <input type="hidden"
        name="{{ $name }}"
        x-model="selectedId">

    {{-- TRIGGER BOX --}}
    <div x-ref="trigger"
        @click="open = !open"
        class="mt-1 relative w-full cursor-pointer border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 rounded-md shadow-sm py-2.5 px-3 text-left sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 hover:border-indigo-400">

        <span class="block truncate text-gray-700 dark:text-gray-300 font-mono text-xs md:text-sm"
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
    </div>

    {{-- DROPDOWN LIST --}}
    <div x-show="open"
        style="display: none;"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 w-full md:min-w-full md:w-max max-w-[90vw] bg-white dark:bg-gray-800 shadow-xl max-h-60 rounded-md ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600"
        :class="dropUp ? 'bottom-full mb-2' : 'top-full mt-1'">

        {{-- DYNAMIC HEADER --}}
        <div
            class="grid grid-cols-12 gap-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 font-bold text-xs uppercase text-gray-600 dark:text-gray-300 sticky top-0 z-10">
            @foreach ($columns as $col)
                <div class="col-span-{{ $col['span'] ?? 1 }} {{ $col['align'] ?? 'text-left' }}">
                    {{ $col['header'] }}
                </div>
            @endforeach
        </div>

        {{-- DYNAMIC BODY --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <template x-for="item in items"
                :key="item.id">
                <div @click="selectOption(item)"
                    class="cursor-pointer select-none relative py-2.5 px-4 hover:bg-indigo-600 hover:text-white group transition-colors"
                    :class="{ 'bg-indigo-50 dark:bg-indigo-900/50': selectedId == item.id }">

                    <div class="grid grid-cols-12 gap-4 items-center text-xs md:text-sm">
                        @foreach ($columns as $col)
                            <div class="col-span-{{ $col['span'] ?? 1 }} {{ $col['align'] ?? 'text-left' }} truncate {{ $col['class'] ?? '' }}"
                                :class="{ 'text-gray-500 dark:text-gray-400 group-hover:text-indigo-200': {{ $loop->index > 0 ? 'true' : 'false' }} }"
                                x-text="item.{{ $col['key'] }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </template>

            <template x-if="items.length === 0">
                <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-xs">
                    No options available.
                </div>
            </template>
        </div>
    </div>
</div>
