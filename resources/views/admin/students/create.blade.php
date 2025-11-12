<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Student') }}
            </h2>
            <a href="{{ route('admin.students.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                {{ __('Back to Students') }}
            </a>
        </div>
    </x-slot>

    <div class="py-9">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST"
                        action="{{ route('admin.students.store') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-8">
                            <!-- User Information Section -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    User Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <x-input-label for="name"
                                            :value="__('Full Name')" />
                                        <x-text-input id="name"
                                            name="name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('name')"
                                            required
                                            autofocus />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('name')" />
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <x-input-label for="email"
                                            :value="__('Email')" />
                                        <x-text-input id="email"
                                            name="email"
                                            type="email"
                                            class="mt-1 block w-full"
                                            :value="old('email')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('email')" />
                                    </div>

                                    <!-- Username -->
                                    <div>
                                        <x-input-label for="username"
                                            :value="__('Username')" />
                                        <x-text-input id="username"
                                            name="username"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('username')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('username')" />
                                    </div>

                                    <!-- Profile Picture -->
                                    <div>
                                        <x-input-label for="profile_pic"
                                            :value="__('Profile Picture')" />
                                        <input type="file"
                                            id="profile_pic"
                                            name="profile_pic"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300"
                                            accept="image/*">
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('profile_pic')" />
                                    </div>

                                    <!-- Password -->
                                    <div>
                                        <x-input-label for="password"
                                            :value="__('Password')" />
                                        <x-text-input id="password"
                                            name="password"
                                            type="password"
                                            class="mt-1 block w-full"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('password')" />
                                    </div>

                                    <!-- Confirm Password -->
                                    <div>
                                        <x-input-label for="password_confirmation"
                                            :value="__('Confirm Password')" />
                                        <x-text-input id="password_confirmation"
                                            name="password_confirmation"
                                            type="password"
                                            class="mt-1 block w-full"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('password_confirmation')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information Section -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Academic Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Department -->
                                    <div>
                                        <x-input-label for="department_id"
                                            :value="__('Department')" />
                                        <select id="department_id"
                                            name="department_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>
                                            <option value="">Select Department</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('department_id')" />
                                    </div>

                                    <!-- Program -->
                                    <div>
                                        <x-input-label for="program_id"
                                            :value="__('Program')" />
                                        <select id="program_id"
                                            name="program_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>
                                            <option value="">Select Program</option>
                                            @foreach ($programs as $program)
                                                <option value="{{ $program->id }}"
                                                    {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                                    {{ $program->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('program_id')" />
                                    </div>

                                    <!-- Admission Date -->
                                    <div>
                                        <x-input-label for="admission_date"
                                            :value="__('Admission Date')" />
                                        <x-text-input id="admission_date"
                                            name="admission_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            :value="old('admission_date')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('admission_date')" />
                                    </div>

                                    <!-- Enrollment Status -->
                                    <div>
                                        <x-input-label for="enrollment_status"
                                            :value="__('Enrollment Status')" />
                                        <select id="enrollment_status"
                                            name="enrollment_status"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>
                                            <option value="full_time"
                                                {{ old('enrollment_status') == 'full_time' ? 'selected' : '' }}>
                                                Full Time
                                            </option>
                                            <option value="part_time"
                                                {{ old('enrollment_status') == 'part_time' ? 'selected' : '' }}>
                                                Part Time
                                            </option>
                                            <option value="exchange"
                                                {{ old('enrollment_status') == 'exchange' ? 'selected' : '' }}>
                                                Exchange
                                            </option>
                                            <option value="study_abroad"
                                                {{ old('enrollment_status') == 'study_abroad' ? 'selected' : '' }}>
                                                Study Abroad
                                            </option>
                                        </select>
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('enrollment_status')" />
                                    </div>

                                    <!-- Fee Category -->
                                    <div>
                                        <x-input-label for="fee_category"
                                            :value="__('Fee Category')" />
                                        <select id="fee_category"
                                            name="fee_category"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>
                                            <option value="regular"
                                                {{ old('fee_category') == 'regular' ? 'selected' : '' }}>
                                                Regular
                                            </option>
                                            <option value="scholarship"
                                                {{ old('fee_category') == 'scholarship' ? 'selected' : '' }}>
                                                Scholarship
                                            </option>
                                            <option value="financial_aid"
                                                {{ old('fee_category') == 'financial_aid' ? 'selected' : '' }}>
                                                Financial Aid
                                            </option>
                                            <option value="self_financed"
                                                {{ old('fee_category') == 'self_financed' ? 'selected' : '' }}>
                                                Self Financed
                                            </option>
                                        </select>
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('fee_category')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information Section -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Personal Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Date of Birth -->
                                    <div>
                                        <x-input-label for="date_of_birth"
                                            :value="__('Date of Birth')" />
                                        <x-text-input id="date_of_birth"
                                            name="date_of_birth"
                                            type="date"
                                            class="mt-1 block w-full"
                                            :value="old('date_of_birth')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('date_of_birth')" />
                                    </div>

                                    <!-- Gender -->
                                    <div>
                                        <x-input-label for="gender_id"
                                            :value="__('Gender')" />
                                        <select id="gender_id"
                                            name="gender_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>
                                            <option value="">Select Gender</option>
                                            @foreach ($genders as $gender)
                                                <option value="{{ $gender->id }}"
                                                    {{ old('gender_id') == $gender->id ? 'selected' : '' }}>
                                                    {{ $gender->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('gender_id')" />
                                    </div>

                                    <!-- Nationality -->
                                    <div>
                                        <x-input-label for="nationality"
                                            :value="__('Nationality')" />
                                        <x-text-input id="nationality"
                                            name="nationality"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('nationality')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('nationality')" />
                                    </div>

                                    <!-- Phone -->
                                    <div>
                                        <x-input-label for="phone"
                                            :value="__('Phone Number')" />
                                        <x-text-input id="phone"
                                            name="phone"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('phone')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('phone')" />
                                    </div>

                                    <!-- Blood Group -->
                                    <div>
                                        <x-input-label for="blood_group"
                                            :value="__('Blood Group')" />
                                        <x-text-input id="blood_group"
                                            name="blood_group"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('blood_group')" />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('blood_group')" />
                                    </div>

                                    <!-- Has Disability -->
                                    <div class="flex items-center">
                                        <input id="has_disability"
                                            name="has_disability"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900"
                                            {{ old('has_disability') ? 'checked' : '' }} />
                                        <x-input-label for="has_disability"
                                            :value="__('Has Disability')"
                                            class="ml-2" />
                                    </div>

                                    <!-- Disability Details -->
                                    <div class="md:col-span-2">
                                        <x-input-label for="disability_details"
                                            :value="__('Disability Details')" />
                                        <textarea id="disability_details"
                                            name="disability_details"
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('disability_details') }}</textarea>
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('disability_details')" />
                                    </div>

                                    <!-- Previous Education -->
                                    <div class="md:col-span-2">
                                        <x-input-label for="previous_education"
                                            :value="__('Previous Education')" />
                                        <textarea id="previous_education"
                                            name="previous_education"
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('previous_education') }}</textarea>
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('previous_education')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information Section -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Address Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Current Address -->
                                    <div class="md:col-span-2">
                                        <x-input-label for="current_address"
                                            :value="__('Current Address')" />
                                        <textarea id="current_address"
                                            name="current_address"
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>{{ old('current_address') }}</textarea>
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('current_address')" />
                                    </div>

                                    <!-- Permanent Address -->
                                    <div class="md:col-span-2">
                                        <x-input-label for="permanent_address"
                                            :value="__('Permanent Address')" />
                                        <textarea id="permanent_address"
                                            name="permanent_address"
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required>{{ old('permanent_address') }}</textarea>
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('permanent_address')" />
                                    </div>

                                    <!-- City -->
                                    <div>
                                        <x-input-label for="city"
                                            :value="__('City')" />
                                        <x-text-input id="city"
                                            name="city"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('city')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('city')" />
                                    </div>

                                    <!-- State -->
                                    <div>
                                        <x-input-label for="state"
                                            :value="__('State')" />
                                        <x-text-input id="state"
                                            name="state"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('state')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('state')" />
                                    </div>

                                    <!-- Country -->
                                    <div>
                                        <x-input-label for="country"
                                            :value="__('Country')" />
                                        <x-text-input id="country"
                                            name="country"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('country')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('country')" />
                                    </div>

                                    <!-- Postal Code -->
                                    <div>
                                        <x-input-label for="postal_code"
                                            :value="__('Postal Code')" />
                                        <x-text-input id="postal_code"
                                            name="postal_code"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('postal_code')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('postal_code')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Emergency Contact Section -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                    Emergency Contact
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Emergency Contact Name -->
                                    <div>
                                        <x-input-label for="emergency_contact_name"
                                            :value="__('Contact Name')" />
                                        <x-text-input id="emergency_contact_name"
                                            name="emergency_contact_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('emergency_contact_name')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('emergency_contact_name')" />
                                    </div>

                                    <!-- Emergency Contact Phone -->
                                    <div>
                                        <x-input-label for="emergency_contact_phone"
                                            :value="__('Contact Phone')" />
                                        <x-text-input id="emergency_contact_phone"
                                            name="emergency_contact_phone"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('emergency_contact_phone')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('emergency_contact_phone')" />
                                    </div>

                                    <!-- Emergency Contact Relation -->
                                    <div>
                                        <x-input-label for="emergency_contact_relation"
                                            :value="__('Relationship')" />
                                        <x-text-input id="emergency_contact_relation"
                                            name="emergency_contact_relation"
                                            type="text"
                                            class="mt-1 block w-full"
                                            :value="old('emergency_contact_relation')"
                                            required />
                                        <x-input-error class="mt-2"
                                            :messages="$errors->get('emergency_contact_relation')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-end space-x-4 pt-4">
                                <a href="{{ route('admin.students.index') }}"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                                    {{ __('Cancel') }}
                                </a>
                                <x-primary-button class="">
                                    {{ __('Create Student') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
