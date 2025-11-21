<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Faculty Details') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Faculty #{{ $faculty->id }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label :value="__('Student')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $faculty->name }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Student')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $faculty->code }}
                        </p>
                    </div>

                    <div>
                        <x-input-label :value="__('Student')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $faculty->dean->user->name ?? 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <x-input-label :value="__('Student')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $faculty->created_at }}
                        </p>
                    </div>

                    <div>
                        <x-input-label :value="__('Student')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ $faculty->updated_at }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <a href="{{ route('admin.faculties.edit', $faculty) }}"
                    class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('admin.faculties.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
