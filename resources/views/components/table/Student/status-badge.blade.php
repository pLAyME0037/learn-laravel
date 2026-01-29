@props(['value'])

<div>
    @php
        $colorClass = match (strtolower($value)) {
        // Academic Statuses
        'active'      => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 border-green-200',
        'probation'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 border-yellow-200',
        'graduated'   => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 border-indigo-200',
        'suspended'   => 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300 border-orange-200',
        'withdrawn'   => 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300 border-rose-200',
        'transferred' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/50 dark:text-teal-300 border-teal-200',
        'trashed'     => 'bg-red-100 text-red-500 dark:bg-red-900/50 dark:text-red-300 border-red-300 decoration-line-through',

        // Roles (If re-used)
        'super administrator' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300 border-purple-200',
        'admin'               => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 border-blue-200',

        default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-200',
    };
    @endphp

    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $colorClass }}">
        {{ ucfirst(str_replace('_', ' ', $value)) }}
    </span>
</div>
