<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

@props(['id', 'name', 'placeholder' => 'Select Date'])

<div x-data="{
    value: @entangle($attributes->wire('model')),
    {{-- Bind directly to Livewire --}}
    instance: null,
    init() {
        this.instance = flatpickr(this.$refs.input, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'F j, Y',
            defaultDate: this.value,
            {{-- Use the entangled value --}}
            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
            onChange: (selectedDates, dateStr, instance) => {
                this.value = dateStr;
            }
        });

        // Watch for updates from Livewire (e.g., loading from DB)
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
        placeholder="{{ $placeholder }}"
        {{ $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }} />
</div>
