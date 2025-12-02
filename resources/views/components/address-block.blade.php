@props(['student'])

<div class="text-sm">
    {{-- 1. Main Address --}}
    @if ($student->address?->current_address)
        <div class="font-normal text-gray-900 dark:text-gray-200 mb-0.5 truncate max-w-xs"
            title="{{ $student->address->current_address }}">
            {{ $student->address->current_address }}
        </div>
    @endif

    {{-- 2. Details --}}
    <div class="flex flex-wrap items-center gap-x-1 text-xs text-gray-500 dark:text-gray-400 leading-tight">
        @php
            $parts = array_filter([
                $student->address?->village ? 'Village: ' . $student->address->village : null,
                $student->address?->commune ? 'Commune: ' . $student->address->commune : null,
                $student->address?->district ? 'District: ' . $student->address->district : null,
                $student->address?->city ? 'City: ' . $student->address->city : null,
            ]);
        @endphp

        @foreach ($parts as $part)
            {{-- Keep label and value together on one line --}}
            <span class="whitespace-nowrap">{{ $part }}</span>
        @endforeach
    </div>
</div>
