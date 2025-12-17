    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit="save">
            <div class="space-y-8">

                @foreach ($schema as $groupName => $fields)
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                        <div
                            class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                {{ $groupName }} Settings
                            </h3>
                        </div>

                        <div class="px-4 py-5 sm:p-6 space-y-6">
                            @foreach ($fields as $key => $config)
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                                    <!-- Label -->
                                    <label for="{{ $key }}"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 md:col-span-1">
                                        {{ $config['label'] }}
                                    </label>

                                    <!-- Input Area -->
                                    <div class="md:col-span-2">
                                        @if ($config['type'] === 'boolean')
                                            <!-- Toggle Switch -->
                                            <div class="flex items-center">
                                                <button type="button"
                                                    wire:click="$toggle('settings.{{ $key }}')"
                                                    class="{{ $settings[$key] ? 'bg-indigo-600' : 'bg-gray-200' }} relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                    role="switch"
                                                    aria-checked="{{ $settings[$key] ? 'true' : 'false' }}">
                                                    <span aria-hidden="true"
                                                        class="{{ $settings[$key] ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                                </button>
                                                <span class="ml-3 text-sm text-gray-500">
                                                    {{ $settings[$key] ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </div>
                                        @elseif($config['type'] === 'number')
                                            <input type="number"
                                                id="{{ $key }}"
                                                wire:model="settings.{{ $key }}"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        @else
                                            <!-- Text / URL / Email -->
                                            <input type="text"
                                                id="{{ $key }}"
                                                wire:model="settings.{{ $key }}"
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>

            <!-- Sticky Save Button -->
            <div class="mt-8 flex justify-end sticky bottom-6">
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg border dark:border-gray-700">
                    <a href="{{ url('/admin/dashboard') }}"
                        class="inline-flex justify-center rounded-md border border-transparent bg-red-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50">
                        <span wire:loading.remove>Cancel</span>
                    </a>
                </div>
                <div class="ml-3 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg border dark:border-gray-700">
                    <button type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50">
                        <span wire:loading.remove>Save Changes</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
