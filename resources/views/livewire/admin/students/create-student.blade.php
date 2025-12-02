<div class="py-9">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">

                <!-- Display System Errors if Service Fails -->
                @error('system_error')
                    <div class="mb-4 p-4 text-red-700 bg-red-100 rounded-lg">
                        {{ $message }}
                    </div>
                @enderror

                <!-- FORM START -->
                <form wire:submit="save"
                    enctype="multipart/form-data">

                    <div class="space-y-8">
                        <!-- ================= USER INFO ================= -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                User Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <x-input-label for="profile_pic"
                                        :value="__('Profile Picture')" />

                                    <div class="mt-2 flex items-center space-x-4">
                                        <x-profile-image
                                            size="xl"
                                            :src="$profile_pic ? $profile_pic->temporaryUrl() : asset('default-avatar.png')" 
                                            class="bg-gray-700"
                                            :uploadable="true"
                                            wire:model="profile_pic" />
                                    </div>

                                    <!-- Hidden field for profile picture removal -->
                                    <input type="hidden"
                                        id="remove-profile-pic"
                                        name="remove_profile_pic"
                                        value="0">
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('profile_pic')" />
                                </div>

                                <div>
                                    <x-input-label for="name"
                                        :value="__('Full Name')" />
                                    <x-text-input id="name"
                                        wire:model="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                        autofocus />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="email"
                                        :value="__('Email')" />
                                    <x-text-input id="email"
                                        wire:model="email"
                                        type="email"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('email')" />
                                </div>

                                <div>
                                    <x-input-label for="username"
                                        :value="__('Username')" />
                                    <x-text-input id="username"
                                        wire:model="username"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('username')" />
                                </div>

                                <div>
                                    <x-input-label for="password"
                                        :value="__('Password')" />
                                    <x-text-input id="password"
                                        wire:model="password"
                                        type="password"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('password')" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation"
                                        :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation"
                                        wire:model="password_confirmation"
                                        type="password"
                                        class="mt-1 block w-full"
                                        required />
                                </div>
                            </div>
                        </div>

                        <!-- ================= ACADEMIC INFO ================= -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Academic Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- DEPARTMENT (Triggers Livewire Update) -->
                                <div>
                                    <x-input-label for="department_id"
                                        :value="__('Department')" />
                                    <select id="department_id"
                                        wire:model.live="department_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('department_id')" />
                                </div>

                                <!-- PROGRAM (Filtered by PHP) -->
                                <div>
                                    <x-input-label for="program_id"
                                        :value="__('Program')" />
                                    <select id="program_id"
                                        wire:model="program_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                        {{ empty($programs) ? 'disabled' : '' }}>
                                        <option value="">Select Program</option>
                                        @foreach ($programs as $program)
                                            <option value="{{ $program->id }}">
                                                {{ $program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('program_id')" />
                                </div>

                                <div>
                                    <x-input-label for="admission_date"
                                        :value="__('Admission Date')" />
                                    <x-date-picker id="admission_date"
                                        name="admission_date"
                                        wire:model="admission_date"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('admission_date')" />
                                </div>

                                <div>
                                    <x-input-label for="expected_graduation"
                                        :value="__('Expected Graduation Date')" />
                                    <x-date-picker id="expected_graduation"
                                        name="expected_graduation"
                                        wire:model="expected_graduation"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('expected_graduation')" />
                                </div>

                                <div>
                                    <x-input-label for="enrollment_status"
                                        :value="__('Enrollment Status')" />
                                    <select id="enrollment_status"
                                        wire:model="enrollment_status"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Status</option>
                                        <option value="full_time">Full Time</option>
                                        <option value="part_time">Part Time</option>
                                        <option value="exchange">Exchange</option>
                                        <option value="study_abroad">Study Abroad</option>
                                    </select>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('enrollment_status')" />
                                </div>

                                <div>
                                    <x-input-label for="fee_category"
                                        :value="__('Fee Category')" />
                                    <select id="fee_category"
                                        wire:model="fee_category"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Category</option>
                                        <option value="regular">Regular</option>
                                        <option value="scholarship">Scholarship</option>
                                        <option value="financial_aid">Financial Aid</option>
                                        <option value="self_financed">Self Financed</option>
                                    </select>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('fee_category')" />
                                </div>


                                <div>
                                    <x-input-label for="academic_status"
                                        :value="__('Academic Status')" />
                                    <select id="academic_status"
                                        wire:model="academic_status"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                        <option value="active">Active</option>
                                        <option value="graduated">Graduated</option>
                                        <option value="suspended">Suspended</option>
                                        <option value="probation">Probation</option>
                                    </select>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('academic_status')" />
                                </div>
                            </div>
                        </div>

                        <!-- ================= PERSONAL INFO ================= -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Personal Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="date_of_birth"
                                        :value="__('Date of Birth')" />
                                    <x-date-picker type="date"
                                        id="date_of_birth"
                                        name="date_of_birth"
                                        wire:model="date_of_birth"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('date_of_birth')" />
                                </div>

                                <div>
                                    <x-input-label for="gender_id"
                                        :value="__('Gender')" />
                                    <select id="gender_id"
                                        wire:model="gender_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm">
                                        <option value="">Select Gender</option>
                                        @foreach ($genders as $gender)
                                            <option value="{{ $gender->id }}">
                                                {{ $gender->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('gender_id')" />
                                </div>

                                <div>
                                    <x-input-label for="nationality"
                                        :value="__('Nationality')" />
                                    <x-text-input id="nationality"
                                        wire:model="nationality"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('nationality')" />
                                </div>

                                <!-- NESTED: Phone -->
                                <div>
                                    <x-input-label for="contact_detail.phone_number"
                                        :value="__('Phone Number')" />
                                    <x-text-input id="contact_detail.phone_number"
                                        wire:model="contact_detail.phone_number"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('contact_detail.phone_number')" />
                                </div>

                                <div>
                                    <x-input-label for="blood_group"
                                        :value="__('Blood Group')" />
                                    <x-text-input id="blood_group"
                                        wire:model="blood_group"
                                        type="text"
                                        class="mt-1 block w-full" />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('blood_group')" />
                                </div>

                                <div class="flex items-center">
                                    <input id="has_disability"
                                        wire:model="has_disability"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900" />
                                    <x-input-label for="has_disability"
                                        :value="__('Has Disability')"
                                        class="ml-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="disability_details"
                                        :value="__('Disability Details')" />
                                    <textarea id="disability_details"
                                        wire:model="disability_details"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                        {{ !$has_disability ? 'disabled' : '' }}></textarea>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('disability_details')" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="previous_education"
                                        :value="__('Previous Education')" />
                                    <textarea id="previous_education"
                                        wire:model="previous_education"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"></textarea>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('previous_education')" />
                                </div>
                            </div>
                        </div>

                        <!-- ================= ADDRESS INFO ================= -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Address Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-input-label for="address.current_address"
                                        :value="__('Current Address')" />
                                    <textarea id="address.current_address"
                                        wire:model="address.current_address"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                        required></textarea>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('address.current_address')" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="address.permanent_address"
                                        :value="__('Permanent Address')" />
                                    <textarea id="address.permanent_address"
                                        wire:model="address.permanent_address"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 rounded-md shadow-sm"
                                        required></textarea>
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('address.permanent_address')" />
                                </div>

                                <div>
                                    <x-input-label for="address.city"
                                        :value="__('City')" />
                                    <x-text-input id="address.city"
                                        wire:model="address.city"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('address.city')" />
                                </div>

                                <div>
                                    <x-input-label for="address.district"
                                        :value="__('District')" />
                                    <x-text-input id="address.district"
                                        wire:model="address.district"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('address.district')" />
                                </div>

                                <div>
                                    <x-input-label for="address.commune"
                                        :value="__('Commune')" />
                                    <x-text-input id="address.commune"
                                        wire:model="address.commune"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('address.commune')" />
                                </div>

                                <div>
                                    <x-input-label for="address.village"
                                        :value="__('Village')" />
                                    <x-text-input id="address.village"
                                        wire:model="address.village"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('address.village')" />
                                </div>

                                <div>
                                    <x-input-label for="address.postal_code"
                                        :value="__('Postal Code')" />
                                    <x-text-input id="address.postal_code"
                                        wire:model="address.postal_code"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('address.postal_code')" />
                                </div>
                            </div>
                        </div>

                        <!-- ================= EMERGENCY CONTACT ================= -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                Emergency Contact
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="contact_detail.emergency_contact_name"
                                        :value="__('Contact Name')" />
                                    <x-text-input id="contact_detail.emergency_contact_name"
                                        wire:model="contact_detail.emergency_contact_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('contact_detail.emergency_contact_name')" />
                                </div>

                                <div>
                                    <x-input-label for="contact_detail.emergency_contact_phone"
                                        :value="__('Contact Phone')" />
                                    <x-text-input id="contact_detail.emergency_contact_phone"
                                        wire:model="contact_detail.emergency_contact_phone"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('contact_detail.emergency_contact_phone')" />
                                </div>

                                <div>
                                    <x-input-label for="contact_detail.emergency_contact_relation"
                                        :value="__('Relationship')" />
                                    <x-text-input id="contact_detail.emergency_contact_relation"
                                        wire:model="contact_detail.emergency_contact_relation"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required />
                                    <x-input-error class="mt-2"
                                        :messages="$errors->get('contact_detail.emergency_contact_relation')" />
                                </div>
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <a href="{{ route('admin.students.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('Create Student') }}</span>
                                <span wire:loading>{{ __('Processing...') }}</span>
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
