<x-app-layout title="Create Payment"
    :pageTitle="__('Create New Payment')">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                Create New Payment
            </h1>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg p-6">
            <form method="POST"
                action="{{ route('admin.payments.store') }}">
                @csrf

                <div>
                    <x-input-label for="student_id"
                        :value="__('Student')" />
                    <select id="student_id"
                        name="student_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required>
                        <option value="">{{ __('Select Student') }}</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}"
                                {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->display_label }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('student_id')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="academic_year_id"
                        :value="__('Academic Year')" />
                    <select id="academic_year_id"
                        name="academic_year_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required>
                        <option value="">{{ __('Select Academic Year') }}</option>
                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}"
                                {{ old('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                {{ $academicYear->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('academic_year_id')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="semester_id"
                        :value="__('Semester')" />
                    <select id="semester_id"
                        name="semester_id"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required>
                        <option value="">{{ __('Select Semester') }}</option>
                        @foreach ($semesters as $semester)
                            <option value="{{ $semester->id }}"
                                {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                {{ $semester->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('semester_id')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="amount"
                        :value="__('Amount')" />
                    <x-text-input id="amount"
                        class="block mt-1 w-full"
                        type="number"
                        step="0.01"
                        name="amount"
                        :value="old('amount')"
                        required
                        autocomplete="amount" />
                    <x-input-error :messages="$errors->get('amount')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="payment_date"
                        :value="__('Payment Date')" />
                    <x-text-input id="payment_date"
                        class="block mt-1 w-full"
                        type="date"
                        name="payment_date"
                        :value="old('payment_date')"
                        required
                        autocomplete="payment_date" />
                    <x-input-error :messages="$errors->get('payment_date')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="method"
                        :value="__('Payment Method')" />
                    <select id="method"
                        name="method"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required>
                        <option value="">{{ __('Select Method') }}</option>
                        <option value="Cash"
                            {{ old('method') == 'Cash' ? 'selected' : '' }}>
                            Cash
                        </option>
                        <option value="Credit Card"
                            {{ old('method') == 'Credit Card' ? 'selected' : '' }}>
                            Credit Card
                        </option>
                        <option value="Bank Transfer"
                            {{ old('method') == 'Bank Transfer' ? 'selected' : '' }}>
                            Bank Transfer
                        </option>
                        <option value="Other"
                            {{ old('method') == 'Other' ? 'selected' : '' }}>
                            Other
                        </option>

                    </select>
                    <x-input-error :messages="$errors->get('method')"
                        class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="status"
                        :value="__('Status')" />
                    <select id="status"
                        name="status"
                        class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required>
                        <option value="">{{ __('Select Status') }}</option>
                        <option value="Pending"
                            {{ old('status') == 'Pending' ? 'selected' : '' }}>
                            Pending
                        </option>
                        <option value="Completed"
                            {{ old('status') == 'Completed' ? 'selected' : '' }}>
                            Completed
                        </option>
                        <option value="Failed"
                            {{ old('status') == 'Failed' ? 'selected' : '' }}>
                            Failed
                        </option>
                    </select>
                    <x-input-error :messages="$errors->get('status')"
                        class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-secondary-button href="{{ route('admin.payments.index') }}"
                        class="ml-4">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-primary-button class="ml-4">
                        {{ __('Create Payment') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
