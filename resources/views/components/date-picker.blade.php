@props(['id', 'name', 'value' => null, 'placeholder' => 'Select Date'])

<div x-data="{
    value: '{{ $value }}', // Default for standard Blade
    instance: null,
    init() {
        // If Livewire is present, bind to it. Otherwise use standard value.
        @if ($attributes->has('wire:model')) this.value = @entangle($attributes->wire('model')); @endif

        this.instance = flatpickr(this.$refs.input, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'F j, Y',
            defaultDate: this.value,
            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
            onChange: (selectedDates, dateStr, instance) => {
                this.value = dateStr;
                // Force update hidden input for standard Blade forms
                this.$refs.input.value = dateStr;
            }
        });

        // Watch for external changes (Livewire updates)
        this.$watch('value', (newValue) => {
            if (this.instance && newValue !== this.instance.input.value) {
                this.instance.setDate(newValue, false);
            }
        });
    }
}"
    wire:ignore>

    <input x-ref="input"
        type="text"
        id="{{ $id }}"
        name="{{ $name }}"
        x-model="value"
        {{-- Bind Alpine state --}}
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }} />
</div>
