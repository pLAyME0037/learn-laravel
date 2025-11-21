<x-app-layout title="Edit Role"
    :pageTitle="__('Edit Role')">
    <div class="container mx-auto px-4 py-8 max-w-7xl">

        <!-- Header Section -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    {{ $role->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Update role details and configure access permissions.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700">
                    ID: {{ $role->id }}
                </span>
            </div>
        </div>

        <form method="POST"
            action="{{ route('admin.roles.update', $role->id) }}"
            class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Section 1: Role Information -->
            <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Role Details
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <x-input-label for="name"
                            :value="__('Role Name')"
                            class="!text-base !mb-2" />
                        <div class="relative">
                            <x-text-input id="name"
                                class="block w-full pl-11"
                                type="text"
                                name="name"
                                :value="old('name', $role->name)"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="e.g. Academic Manager" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Unique name used to identify this role in the system.
                        </p>
                        <x-input-error :messages="$errors->get('name')"
                            class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Section 2: Permissions Matrix -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Access Permissions
                    </h3>
                    <span
                        class="text-sm text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                        {{ count($rolePermissions) }} Selected
                    </span>
                </div>

                {{-- Permission Assignment Section --}}
                <div class="space-y-6 mt-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11.543 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                </path>
                            </svg>
                            Assign Direct Permissions
                        </h3>
                    </div>

                    @foreach ($permissions as $groupName => $groupPermissions)
                        <x-permissions-section :group="$groupName"
                            :permissions="$groupPermissions"
                            :selected="$rolePermissions" />
                    @endforeach
                </div>
            </div>

            <!-- Sticky Actions Footer -->
            <div
                class="sticky bottom-0 z-10 -mx-4 -mb-8 px-4 py-4 bg-white/80 dark:bg-gray-800/90 backdrop-blur-md border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                <x-secondary-button href="{{ route('admin.roles.show', $role) }}">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="shadow-lg shadow-indigo-500/30">
                    {{ __('Save Changes') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
