@props(['action', 'row'])

{{-- Early return if the action condition is not met --}}
@if (isset($action['condition']) && !$action['condition']($row))
    @return
@endif
{{-- Centralize route parameter building (DRY Principle) --}}
@php
    $routeParams = [];
    if (isset($action['params']) && is_array($action['params'])) {
        foreach ($action['params'] as $paramKey => $dataKey) {
            $routeParams[$paramKey] = data_get($row, $dataKey);
        }
    }
@endphp

{{-- Use a switch for clarity and extensibility --}}
@switch($action['type'] ?? 'link')
    {{-- Case for a standard link --}}
    @case('link')
        <a href="{{ route($action['route'], $routeParams) }}"
            @class([
                'text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300',
                $action['class'] ?? '',
            ])>
            {{ $action['label'] }}
        </a>
    @break

    {{-- Case for a form-based delete action --}}
    @case('delete')
        <form action="{{ route($action['route'], $routeParams) }}"
            method="POST"
            class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                @class([
                    $action['class'] ?? 'text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300',
                ])
                onclick="return confirm('Are you sure you want to delete this item?');">
                {{ $action['label'] }}
            </button>
        </form>
    @break

    {{-- Case for a form-based POST button --}}
    @case('post-button')
        <form action="{{ route($action['route'], $routeParams) }}"
            method="POST" 
            class="inline">
            @csrf
            @if (isset($action['_method']))
                @method($action['_method'])
            @endif
            <button type="submit"
                @class([
                    $action['class'] ?? 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300',
                ])>
                {{ $action['label'] }}
            </button>
        </form>
    @break

    {{-- Fallback case for a simple span or other custom types --}}

    @default
        <span @class([$action['class'] ?? ''])>
            {{ $action['label'] }}
        </span>
@endswitch
