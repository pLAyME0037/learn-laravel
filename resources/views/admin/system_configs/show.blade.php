<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Configuration Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <x-input-label for="key" :value="__('Key')" />
                        <p>{{ $systemConfig->key }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="value" :value="__('Value')" />
                        <p>{{ $systemConfig->value }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Description')" />
                        <p>{{ $systemConfig->description }}</p>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.system-configs.edit', $systemConfig) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.system-configs.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
