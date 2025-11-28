@props(['itemId' => null])

<tr {{ $attributes->merge(['class' => 'hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors']) }}
    @if ($itemId) :class="{'bg-indigo-50 dark:bg-indigo-900/10': selected.includes({{ $itemId }})}" @endif>
    {{ $slot }}
</tr>
