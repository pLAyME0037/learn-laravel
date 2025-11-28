<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Degree') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST"
                        action="{{ route('admin.degrees.update', $degree->id) }}">
                        @csrf
                        @method('patch')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name"
                                :value="__('Name')" />
                            <x-text-input id="name"
                                class="block mt-1 w-full"
                                type="text"
                                name="name"
                                :value="old('name', $degree->name)"
                                required
                                autofocus />
                            <x-input-error :messages="$errors->get('name')"
                                class="mt-2" />
                        </div>

                        <!-- Level -->
                        <div class="mt-4">
                            <x-input-label for="level"
                                :value="__('Level')" />
                            <x-text-input id="level"
                                class="block mt-1 w-full"
                                type="text"
                                name="level"
                                :value="old('level', $degree->level)"
                                required />
                            <x-input-error :messages="$errors->get('level')"
                                class="mt-2" />
                        </div>

                        <!-- Faculty ID -->
                        <div class="mt-4">
                            <x-input-label for="faculty_id"
                                :value="__('Faculty')" />
                            <select id="faculty_id"
                                name="faculty_id"
                                class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Select Faculty</option>
                                @foreach ($faculties as $faculty)
                                    <option value="{{ $faculty->id }}"
                                        {{ old('faculty_id', $degree->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('faculty_id')"
                                class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.degrees.show', $degree->id) }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
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
