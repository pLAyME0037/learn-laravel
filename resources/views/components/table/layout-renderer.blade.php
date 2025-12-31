{{-- 
    components.table.layout-renderer
--}}
@props(['block', 'row', 'rowIndex', 'paginator'])

@if (is_array($block) && isset($block['type']))

    {{-- A. STACK LAYOUT (Vertical) --}}
    @if ($block['type'] === 'stack')
        <div class="flex flex-col gap-1">
            @foreach ($block['items'] as $item)
                @include('components.table.field-renderer', [
                    'field' => $item,
                    'row' => $row,
                    'rowIndex' => $rowIndex,
                    'paginator' => $paginator,
                ])
            @endforeach
        </div>

        {{-- B. ROW LAYOUT (Horizontal) --}}
    @elseif ($block['type'] === 'row')
        <div class="flex items-center gap-2">
            @foreach ($block['items'] as $item)
                @include('components.table.field-renderer', [
                    'field' => $item,
                    'row' => $row,
                    'rowIndex' => $rowIndex,
                    'paginator' => $paginator,
                ])
            @endforeach
        </div>

        {{-- C. GRID LAYOUT --}}
    @elseif ($block['type'] === 'grid')
        @php
            $template = is_numeric($block['config']) ? "repeat({$block['config']}, minmax(0, 1fr))" : $block['config'];
        @endphp
        <div class="grid gap-3 w-full items-center"
            style="grid-template-columns: {{ $template }};">
            @foreach ($block['items'] as $item)
                @if ($item instanceof \App\Tables\Column)
                    {{-- Recursion for Nested Columns --}}
                    <div class="flex flex-col gap-1">
                        @foreach ($item->fields as $nestedBlock)
                            @include('components.table.layout-renderer', [
                                'block' => $nestedBlock,
                                'row' => $row,
                                'rowIndex' => $rowIndex,
                                'paginator' => $paginator
                            ])
                        @endforeach
                    </div>
                @else
                    @include('components.table.field-renderer', [
                        'field' => $item,
                        'row' => $row,
                        'rowIndex' => $rowIndex,
                        'paginator' => $paginator,
                    ])
                @endif
            @endforeach
        </div>
    @endif

    {{-- D. DIRECT FIELD OBJECT --}}
@elseif (is_object($block))
    @include('components.table.field-renderer', [
        'field' => $block,
        'row' => $row,
        'rowIndex' => $rowIndex,
        'paginator' => $paginator,
    ])
@endif
