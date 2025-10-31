<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit System Configuration') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.system-configs.update', $systemConfig) }}">
                        @csrf
                        @method('PUT')

                        <!-- Key -->
                        <div>
                            <x-input-label for="key" :value="__('Key')" />
                            <x-text-input id="key" class="block mt-1 w-full" type="text" name="key" :value="old('key', $systemConfig->key)" required autofocus />
                            <x-input-error :messages="$errors->get('key')" class="mt-2" />
                        </div>

                        <!-- Value -->
                        <div class="mt-4">
                            <x-input-label for="value" :value="__('Value')" />
                            <x-text-input id="value" class="block mt-1 w-full" type="text" name="value" :value="old('value', $systemConfig->value)" />
                            <x-input-error :messages="$errors->get('value')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <x-textarea-input id="description" class="block mt-1 w-full" name="description">{{ old('description', $systemConfig->description) }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
