@props(['user'])

@if ($user->trashed())
    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
        Deleted
    </span>
@else
    <span @class([
        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
        'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' => $user->is_active,
        'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' => !$user->is_active,
    ])>
        {{ $user->is_active ? 'Active' : 'Inactive' }}
    </span>
@endif