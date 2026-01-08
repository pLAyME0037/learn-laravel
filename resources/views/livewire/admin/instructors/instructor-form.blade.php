<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
            {{ $isEdit ? 'Edit Instructor' : 'Create New Instructor' }}
        </h2>
        <a href="{{ route('admin.instructors.index') }}"
            class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            Cancel
        </a>
    </div>

    <form wire:submit="save"
        class="space-y-8">

        <!-- 1. Account Info -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    User Account
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Full Name -->
                <div>
                    <x-input-label for="name"
                        value="Full Name" />
                    <x-text-input id="name"
                        wire:model.live="user.name"
                        class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('user.name')"
                        class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email"
                        value="Email Address" />
                    <x-text-input id="email"
                        wire:model.live="user.email"
                        type="email"
                        class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('user.email')"
                        class="mt-2" />
                </div>

                <!-- Username (With Generator) -->
                <div class="md:col-span-2">
                    <x-input-label for="username"
                        value="Username" />
                    <div class="flex gap-2">
                        <x-text-input id="username"
                            wire:model="user.username"
                            class="block mt-1 w-full"
                            placeholder="Enter username or generate auto with Generate Button" />
                        <button type="button"
                            wire:click="generateUsername"
                            class="mt-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 text-sm">
                            Generate
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('user.username')"
                        class="mt-2" />
                </div>

                @if (!$isEdit)
                    <!-- Password -->
                    <div>
                        <x-input-label for="password"
                            value="Password" />
                        <x-text-input id="password"
                            wire:model="user.password"
                            type="password"
                            class="block mt-1 w-full" />
                        <x-input-error :messages="$errors->get('user.password')"
                            class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation"
                            value="Confirm Password" />
                        <x-text-input id="password_confirmation"
                            wire:model="user.password_confirmation"
                            type="password"
                            class="block mt-1 w-full" />
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Academic Info -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
            <div
                class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Academic Details
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department -->
                <div>
                    <x-input-label for="department"
                        value="Department" />
                    <select id="department"
                        wire:model="profile.department_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">Select Department</option>
                        @foreach ($departments as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('profile.department_id')"
                        class="mt-2" />
                </div>

                <!-- Staff ID -->
                <div>
                    <x-input-label for="staff_id"
                        value="Staff ID" />
                    <x-text-input id="staff_id"
                        wire:model="profile.staff_id"
                        class="block mt-1 w-full cursor-not-allowed"
                        placeholder="Id will be generate automaticaly in a minute"
                        readonly />
                    <x-input-error :messages="$errors->get('profile.staff_id')"
                        class="mt-2" />
                </div>

                <!-- Specialization -->
                <div class="md:col-span-2">
                    <x-input-label for="specialization"
                        value="Specialization" />
                    <x-text-input id="specialization"
                        wire:model="profile.specialization"
                        class="block mt-1 w-full"
                        placeholder="Job or Degree" />
                    <x-input-error :messages="$errors->get('profile.specialization')"
                        class="mt-2" />
                </div>
            </div>
        </div>

        <!-- 3. Address (Using Child Component) -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
            <div
                class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Address
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <x-input-label for="current_address"
                        value="Street / House Number" />
                    <x-text-input id="current_address"
                        wire:model="address.current_address"
                        class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('address.current_address')"
                        class="mt-2" />
                </div>

                <!-- Location Picker -->
                @livewire('forms.location-picker', ['selectedVillageId' => $address['village_id'] ?? null])
                <x-input-error :messages="$errors->get('address.village_id')"
                    class="mt-2" />
            </div>
        </div>

        <!-- 4. Contact -->
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
            <div
                class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Contact Info
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="phone"
                        value="Phone Number" />
                    <x-text-input id="phone"
                        wire:model="contact.phone"
                        class="block mt-1 w-full" />
                    <x-input-error :messages="$errors->get('contact.phone')"
                        class="mt-2" />
                </div>
                <div>
                    <x-input-label for="emergency_phone"
                        value="Emergency Contact (Optional)" />
                    <x-text-input id="emergency_phone"
                        wire:model="contact.emergency_phone"
                        class="block mt-1 w-full" />
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-4">
            <button type="submit"
                wire:loading.attr="disabled"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg flex items-center disabled:opacity-50">
                <span wire:loading.remove>{{ $isEdit ? 'Update Instructor' : 'Create Instructor' }}</span>
                <span wire:loading
                    class="ml-2">Saving...</span>
            </button>
        </div>

    </form>
</div>
