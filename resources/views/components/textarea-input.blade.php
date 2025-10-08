@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'rows' => 4,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
])

@php
    $classes = [
        'base' =>
            'w-full px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:border-transparent transition duration-200',
        'normal' =>
            'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500',
        'error' =>
            'border-red-500 dark:border-red-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-red-300 dark:placeholder-red-600 focus:ring-red-500 focus:border-red-500',
        'disabled' =>
            'bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400 border-gray-300 dark:border-gray-500 cursor-not-allowed',
    ];

    $inputClass = $classes['base'] . ' ';

    if ($disabled) {
        $inputClass .= $classes['disabled'];
    } elseif ($error) {
        $inputClass .= $classes['error'];
    } else {
        $inputClass .= $classes['normal'];
    }
@endphp

<div class="space-y-2">
    @if ($label)
        <label for="{{ $name }}"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        @if ($disabled) disabled @endif
        @if ($readonly) readonly @endif
        {{ $attributes->merge(['class' => $inputClass]) }}>{{ $value }}</textarea>

    @if ($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif

    @error($name)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
