<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST"
                        action="{{ route('admin.roles.store') }}">
                        @csrf

                        <!-- Name -->
                        <div>
                            <x-input-label for="name"
                                :value="__('Role Name')" />
                            <x-text-input id="name"
                                class="block mt-1 w-full"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus />
                            <x-input-error :messages="$errors->get('name')"
                                class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description"
                                :value="__('Description')" />
                            <x-textarea-input id="description"
                                class="block mt-1 w-full"
                                name="description">{{ old('description') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')"
                                class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Create Role') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
