{{-- 
    components.smart-table
--}}
@props(['config'])

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">

    {{-- 1. Main Table Wrapper --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">

            {{-- 1.1 Header --}}
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @foreach ($config['headers'] as $col)
                        @if (!in_array($col->header, $config['hidden'] ?? []))
                            <th
                                class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider 
                                       {{ $col->width }} text-{{ $col->align }}">
                                {{ $col->header }}
                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>

            {{-- 1.2 Body --}}
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                @forelse($config['rows'] as $row)
                    @php $rowIndex = $loop->index; @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

                        @foreach ($config['headers'] as $col)
                            @if (!in_array($col->header, $config['hidden'] ?? []))
                                <td class="px-6 py-4 whitespace-nowrap text-{{ $col->align }}">

                                    {{-- CELL CONTENT WRAPPER --}}
                                    <div
                                        class="flex flex-col gap-1 {{ $col->align === 'right' ? 'items-end' : ($col->align === 'center' ? 'items-center' : 'items-start') }}">

                                        {{-- Loop through Layout Blocks (Fields/Stacks/Grids) --}}
                                        @foreach ($col->fields as $block)
                                            @include('components.table.layout-renderer', [
                                                'block' => $block,
                                                'row' => $row,
                                                'rowIndex' => $rowIndex, // Pass Loop
                                                'paginator' => $config['rows'], // Pass Paginator
                                            ])
                                        @endforeach

                                    </div>

                                </td>
                            @endif
                        @endforeach

                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($config['headers']) }}"
                            class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                    </path>
                                </svg>
                                <span class="text-lg font-medium">No records found</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 2. Pagination --}}
    @if (method_exists($config['rows'], 'links'))
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            {{ $config['rows']->links() }}
        </div>
    @endif
</div>
