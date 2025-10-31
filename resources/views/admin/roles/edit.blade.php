<x-app-layout title="Edit Role"
    :pageTitle="__('Edit Role')">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                Edit Role
            </h1>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg p-6">
            <form method="POST"
                action="{{ route('admin.roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="name"
                        :value="__('Role Name')" />
                    <x-text-input id="name"
                        class="block mt-1 w-full"
                        type="text"
                        name="name"
                        :value="old('name', $role->name)"
                        required
                        autofocus
                        autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="permissions"
                        :value="__('Permissions')" />
                    <select id="permissions"
                        name="permissions[]"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        multiple>
                        @foreach ($permissions as $groupName => $groupPermissions)
                            <optgroup label="{{ $groupName }}">
                                @foreach ($groupPermissions as $permission)
                                    <option value="{{ $permission->name }}"
                                        {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'selected' : '' }}>
                                        {{ $permission->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('permissions')"
                        class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-secondary-button href="{{ route('admin.roles.show', $role) }}"
                        class="ml-4">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="ml-4">
                        {{ __('Update Role') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
