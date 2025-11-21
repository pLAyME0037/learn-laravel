@props(['headers' => [], 'data' => [], 'options' => []])

@php
    $mergedOptions = array_merge(
        [
            'wrapperClass' => '',
            'tableClass' => '',
        ],
        $options,
    );

    $options['wrapperClass'] = is_array($mergedOptions['wrapperClass'] ?? '')
        ? implode(' ', (array) $mergedOptions['wrapperClass'])
        : (string) ($mergedOptions['wrapperClass'] ?? '');

    $options['tableClass'] = is_array($mergedOptions['tableClass'] ?? '')
        ? implode(' ', (array) $mergedOptions['tableClass'])
        : (string) ($mergedOptions['tableClass'] ?? '');

    $columnCount = count($headers);
@endphp

{{-- Add the ability to merge classes onto the main wrapper --}}
<div
    class="{{ \Illuminate\Support\Arr::toCssClasses([
        'bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg',
        $options['wrapperClass'],
        is_array($attributes->get('class'))
            ? implode(' ', $attributes->get('class') ?? [])
            : (string) ($attributes->get('class') ?? ''),
    ]) }}">
    <div class="overflow-x-auto">
        <table @class(['w-full table-auto', $options['tableClass']])>
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    @foreach ($headers as $label)
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
