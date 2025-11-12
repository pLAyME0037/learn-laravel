<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Classroom') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Add New Classroom
                    </h3>

                    <form method="POST"
                        action="{{ route('admin.classrooms.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name"
                                :value="__('Name')" />
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

                        <!-- Capacity -->
                        <div class="mb-4">
                            <x-input-label for="capacity"
                                :value="__('Capacity')" />
                            <x-text-input id="capacity"
                                class="block mt-1 w-full"
                                type="number"
                                name="capacity"
                                :value="old('capacity')"
                                required />
                            <x-input-error :messages="$errors->get('capacity')"
                                class="mt-2" />
                        </div>
                        
                        <!-- Room Number -->
                        <div class="mb-4">
                            <x-input-label for="room_number"
                                :value="__('Room Number')" />
                            <x-text-input id="room_number"
                                class="block mt-1 w-full"
                                type="text"
                                name="room_number"
                                :value="old('room_number')"
                                required />
                            <x-input-error :messages="$errors->get('room_number')"
                                class="mt-2" />
                        </div>

                        <!-- Type -->
                        <div class="mb-4">
                            <x-input-label for="type"
                                :value="__('Type')" />
                            <x-text-input id="type"
                                class="block mt-1 w-full"
                                type="text"
                                name="type"
                                :value="old('type')"
                                 />
                            <x-input-error :messages="$errors->get('type')"
                                class="mt-2" />
                        </div>

                        <!-- Location -->
                        <div class="mb-4">
                            <x-input-label for="location"
                                :value="__('Location')" />
                            <x-text-input id="location"
                                class="block mt-1 w-full"
                                type="text"
                                name="location"
                                :value="old('location')"
                                 />
                            <x-input-error :messages="$errors->get('location')"
                                class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.classrooms.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Save Classroom') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
