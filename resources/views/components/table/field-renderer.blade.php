{{-- components.table.field-renderer --}}
@if ($field->type === 'component')
    @php $props = $field->resolve($row); @endphp
    <x-dynamic-component :component="$field->componentName"
        :attributes="new \Illuminate\View\ComponentAttributeBag($props)" />
@elseif($field->type === 'index')
    <span class="{{ $field->css }}">
        {{ $paginator->firstItem() + $rowIndex }}
    </span>
@elseif($field->type === 'view')
    @include($field->componentName, $field->resolveViewData($row))
@elseif($field->type === 'html')
    <div class="{{ $field->css }} text-sm">
        {!! $field->resolve($row) !!}
    </div>
@elseif($field->type === 'image')
    <div class="h-10 w-10 flex-shrink-0">
        <img class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-600"
            src="{{ $field->resolve($row) ? Storage::url($field->resolve($row)) : '' }}">
    </div>
@elseif($field->type === 'badge')
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
        {{ ucfirst($field->resolve($row)) }}
    </span>
@elseif($field->type === 'label')
    <span>
        <x-input-label for="{{ $field->resolve($row) }}"
            value="{{ ucfirst($field->resolve($row)) }}"/>
    </span>
@elseif($field->type === 'actions')
    <div class="flex items-center gap-3 justify-end">
        @foreach ($field->actions as $action)
            @if ($action->shouldRender($row))
                @php
                    $icon = $action->resolveIcon($row);
                    $isSvg = str_contains($icon ?? '', '<path'); // Detect if raw SVG
                @endphp

                @if ($action->type === 'link')
                    <a href="{{ $action->resolveUrl($row) }}"
                        class="{{ $action->resolveColor($row) }}"
                        title="{{ $action->label ?? '' }}">
                        @if ($icon)
                            @if($isSvg)
                                <svg class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    {!! $icon !!}
                                </svg>
                            @else
                                <x-dynamic-component :component="$icon"
                                class="w-5 h-5" />
                            @endif
                        @else
                            {{ $action->label }}
                        @endif
                    </a>

                @elseif($action->type === 'button')
                    <button
                        wire:click="{{ $action->method }}({{ $row['id'] ?? '' }})"
                        class="{{ $action->resolveColor($row) }}"
                        title="{{ $action->label ?? '' }}"
                        @if(!empty($action->confirm))
                            wire:confirm="{{ $action->confirm }}"
                        @endif>
                        @if ($icon)
                            @if($isSvg)
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    {!! $icon !!}
                                </svg>
                            @else
                                <x-dynamic-component :component="$icon"
                                class="w-5 h-5" />
                            @endif
                        @else
                            {{ $action->label }}
                        @endif
                    </button>
                @endif
            @endif
        @endforeach
    </div>
@else
{{-- Default Text --}}
<span class="{{ $field->css }} text-sm dark:text-gray-200">
    {{ $field->resolve($row) }}
</span>
@endif
