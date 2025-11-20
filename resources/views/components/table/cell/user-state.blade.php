@props(['user'])

@php
    $dates = [
        'Last Login' => $user->last_login_at,
        'Created At' => $user->created_at,
        'Updated At' => $user->updated_at,
        'Deleted At' => $user->deleted_at,
    ];
@endphp

<div class="flex items-center">
    <div class="ml-4">
        @foreach ($dates as $label => $date)
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $label }}: {{ $date ? $date->diffForHumans() : 'Never' }}
            </div>
        @endforeach
    </div>
</div>
