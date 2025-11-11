<x-app-layout title="Create Program"
    :pageTitle="__('Create New Program')">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                Create New Permission
            </h1>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg p-6">
            <form method="POST"
                action="{{ route('admin.permissions.store') }}">
                @csrf

                <div>
                    <x-input-label for="name"
                        :value="__('Permission Name')" />
                    <x-text-input id="name"
                        class="block mt-1 w-full"
                        type="text"
                        name="name"
                        :value="old('name')"
                        required
                        autofocus
                        autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="group"
                        :value="__('Group')" />
                    <x-text-input id="group"
                        class="block mt-1 w-full"
                        type="text"
                        name="group"
                        :value="old('group', 'web')"
                        autocomplete="group" />
                    <x-input-error :messages="$errors->get('group')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="group_name"
                        :value="__('Group Name')" />
                    <x-text-input id="group_name"
                        class="block mt-1 w-full"
                        type="text"
                        name="group_name"
                        :value="old('group_name')"
                        autocomplete="group_name" />
                    <x-input-error :messages="$errors->get('group_name')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="description"
                        :value="__('Description')" />
                    <x-text-input id="description"
                        class="block mt-1 w-full"
                        type="text"
                        name="description"
                        :value="old('description')"
                        required
                        autocomplete="description" />
                    <x-input-error :messages="$errors->get('description')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="role_id"
                        :value="__('Assign To Role')" />
                    <select id="role_id"
                        name="role_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">{{ __('Select Role') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role_id')"
                        class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-secondary-button href="{{ route('admin.permissions.index') }}"
                        class="ml-4">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="ml-4">
                        {{ __('Create Permission') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
