@props(['user'])

<div class="flex items-center">
    <div class="flex-shrink-0 h-10 w-10">
        <x-profile-image class="border-blue-700"
            src="{{ $user->profile_picture_url }}"
            alt="{{ $user->name }}"
            size="sm" />
    </div>
    <div class="ml-4">
        <div class="text-sm font-medium">
            <a href="{{ route('admin.users.show', $user) }}"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">
                {{ $user->name }}
            </a>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ '@' }}{{ $user->username }}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ $user->email }}
        </div>
    </div>
</div>
