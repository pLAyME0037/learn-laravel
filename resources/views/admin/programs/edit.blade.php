<x-app-layout title="Edit Program"
    :pageTitle="__('Edit Program')">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                Edit Program
            </h1>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg p-6">
            <form method="POST"
                action="{{ route('admin.programs.update', $program->id) }}">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="name"
                        :value="__('Program Name')" />
                    <x-text-input id="name"
                        class="block mt-1 w-full"
                        type="text"
                        name="name"
                        :value="old('name', $program->name)"
                        required
                        autofocus
                        autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="code"
                        :value="__('Program Code')" />
                    <x-text-input id="code"
                        class="block mt-1 w-full"
                        type="text"
                        name="code"
                        :value="old('code', $program->code)"
                        required
                        autocomplete="code" />
                    <x-input-error :messages="$errors->get('code')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="department_id"
                        :value="__('Department')" />
                    <select id="department_id"
                        name="department_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required>
                        <option value="">{{ __('Select Department') }}</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id', $program->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('department_id')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="degree_id"
                        :value="__('Degree')" />
                    <select id="degree_id"
                        name="degree_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required>
                        <option value="">{{ __('Select Degree') }}</option>
                        @foreach ($degrees as $degree)
                            <option value="{{ $degree->id }}"
                                {{ old('degree_id', $program->degree_id) == $degree->id ? 'selected' : '' }}>
                                {{ $degree->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('degree_id')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="description"
                        :value="__('Description')" />
                    <x-textarea-input id="description"
                        class="block mt-1 w-full"
                        name="description">{{ old('description', $program->description) }}</x-textarea-input>
                    <x-input-error :messages="$errors->get('description')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="duration_years"
                        :value="__('Duration (Years)')" />
                    <x-text-input id="duration_years"
                        class="block mt-1 w-full"
                        type="number"
                        name="duration_years"
                        :value="old('duration_years', $program->duration_years)"
                        required
                        min="1" />
                    <x-input-error :messages="$errors->get('duration_years')"
                        class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-secondary-button href="{{ route('admin.programs.show', $program->id) }}"
                        class="ml-4">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="ml-4">
                        {{ __('Update Program') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
