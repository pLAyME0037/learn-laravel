<x-modal name="copy-permissions-modal"
    :show="$errors->isNotEmpty()"
    focusable>
    <form wire:submit.prevent="copyPermissions"
        class="p-6">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Copy Permissions From Another Role') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Select a source role to copy its permissions to the current role.') }}
        </p>

        <div class="mt-6">
            <x-input-label for="source_role"
                value="{{ __('Source Role') }}"
                class="sr-only" />

            <select wire:model="sourceRoleId"
                id="source_role"
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full">
                <option value="">{{ __('Select a role') }}</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <x-input-error :messages="$errors->get('sourceRoleId')"
                class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-primary-button class="ms-3"
                :disabled="!$sourceRoleId">
                {{ __('Copy Permissions') }}
            </x-primary-button>
        </div>
    </form>
</x-modal>
