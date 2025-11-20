@props(['header', 'data', 'options' => []])

@php
    $options = array_merge(
        [
            'wrapperClass' => '',
            'tableClass' => '',
        ],
        $options,
    );

    // Ensure wrapperClass is a string, converting array to space-separated string if necessary
    if (is_array($options['wrapperClass'])) {
        $options['wrapperClass'] = implode(' ', (array) $options['wrapperClass']);
    }

    // Ensure tableClass is a string, converting array to space-separated string if necessary
    if (is_array($options['tableClass'])) {
        $options['tableClass'] = implode(' ', (array) $options['tableClass']);
    }

    $columnCount = count($header);
@endphp

{{-- Add the ability to merge classes onto the main wrapper --}}
<div class="{{ \Illuminate\Support\Arr::toCssClasses([
    'bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg',
    $options['wrapperClass'],
    $attributes->get('class'),
]) }}"
    {{ $attributes->except('class') }}>
    <div class="overflow-x-auto">
        <table @class(['w-full table-auto', $options['tableClass']])>
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @foreach ($header as $label)
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ $label }}
                        </th>
                    @endforeach

                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                {{ $bodyContent ?? '' }}
            </tbody>
        </table>
    </div>

    @if (isset($pagination) && $data->hasPages())
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ $pagination }}
        </div>
    @endif
</div>
