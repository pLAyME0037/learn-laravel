<x-modal name="copy-user-permissions-modal"
    :show="$errors->isNotEmpty()"
    focusable>
    <form wire:submit.prevent="copyPermissions"
        class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Copy Permissions From...') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Select whether to copy permissions from another user or an existing role.') }}
        </p>

        <div class="mt-6">
            <x-input-label for="source_type"
                value="{{ __('Copy From') }}" />
            <select wire:model.live="sourceType"
                id="source_type"
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full">
                <option value="role">{{ __('Another Role') }}</option>
                <option value="user">{{ __('Another User') }}</option>
            </select>
        </div>

        <div class="mt-4">
            @if ($sourceType === 'role')
                <x-input-label for="source_role_id"
                    value="{{ __('Source Role(s)') }}" />
                <select wire:model="selectedSourceIds"
                    id="source_role_id"
                    multiple
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            @elseif ($sourceType === 'user')
                <x-input-label for="source_user_id"
                    value="{{ __('Source User(s)') }}" />
                <select wire:model="selectedSourceIds"
                    id="source_user_id"
                    multiple
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            @endif
            <x-input-error :messages="$errors->get('selectedSourceIds')"
                class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ms-3"
                :disabled="empty($selectedSourceIds)">
                {{ __('Copy Permissions') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>
