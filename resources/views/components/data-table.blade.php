@props(['rows', 'columns', 'selectable' => false, 'sortCol' => 'id', 'sortDir' => 'desc', 'withActions' => false])

<div x-data="{
    selected: [],
    allSelected: false,
    toggleAll() {
        this.allSelected = !this.allSelected;
        if (this.allSelected) {
            this.selected = {{ Js::from($rows->pluck('id')) }};
        } else {
            this.selected = [];
        }
    }
}"
    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">

    {{-- TOP BAR: Bulk Actions --}}
    @if ($selectable)
        <div x-show="selected.length > 0"
            x-transition
            class="px-6 py-4 bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-between border-b border-indigo-100 dark:border-indigo-800">
            <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">
                <span x-text="selected.length"></span> items selected
            </span>
            <button type="button"
                class="text-xs text-red-600 hover:text-red-800 font-bold uppercase">
                Delete Selected
            </button>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    {{-- SELECT ALL CHECKBOX --}}
                    @if ($selectable)
                        <th scope="col"
                            class="px-1 py-1 bg-gray-50 dark:bg-gray-700 w-4">
                            <input type="checkbox"
                                @click="toggleAll()"
                                :checked="allSelected"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-900 dark:border-gray-600">
                        </th>
                    @endif

                    {{-- DYNAMIC HEADERS --}}
                    @foreach ($columns as $col)
                        <x-table.heading :sortable="$col['key'] ?? null"
                            :direction="$sortCol === ($col['key'] ?? '') ? $sortDir : null"
                            :align="$col['align'] ?? 'left'"
                            :width="$col['width'] ?? ''"
                            {{-- Pass width here --}}>
                            {{ $col['label'] }}
                        </x-table.heading>
                    @endforeach

                    @if ($withActions)
                        <x-table.heading align="right">Actions</x-table.heading>
                    @endif
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                {{-- 
                    WE RENDER THE BODY SLOT HERE.
                    The user loops through rows in the parent view.
                --}}
                {{ $body ?? '' }}
            </tbody>
        </table>
    </div>

    {{-- FOOTER --}}
    @if ($rows->hasPages())
        <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $rows->links() }}
        </div>
    @endif
</div>
