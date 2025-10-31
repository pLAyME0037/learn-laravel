<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Student') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.students.update', $student) }}">
                        @csrf
                        @method('PUT')

                        <!-- User ID -->
                        <div>
                            <x-input-label for="user_id" :value="__('User')" />
                            <x-select-input id="user_id" class="block mt-1 w-full" name="user_id" required autofocus>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id', $student->user_id) == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        <!-- Student ID -->
                        <div class="mt-4">
                            <x-input-label for="student_id" :value="__('Student ID')" />
                            <x-text-input id="student_id" class="block mt-1 w-full" type="text" name="student_id" :value="old('student_id', $student->student_id)" required />
                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <!-- Date of Birth -->
                        <div class="mt-4">
                            <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                            <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth" :value="old('date_of_birth', $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') : '')" required />
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                        </div>

                        <!-- Gender -->
                        <div class="mt-4">
                            <x-input-label for="gender_id" :value="__('Gender')" />
                            <x-select-input id="gender_id" class="block mt-1 w-full" name="gender_id" required>
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender->id }}" @selected(old('gender_id', $student->gender_id) == $gender->id)>{{ $gender->name }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('gender_id')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div class="mt-4">
                            <x-input-label for="address" :value="__('Address')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $student->address)" />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Phone Number -->
                        <div class="mt-4">
                            <x-input-label for="phone_number" :value="__('Phone Number')" />
                            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number', $student->phone_number)" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>

                        <!-- Enrollment Date -->
                        <div class="mt-4">
                            <x-input-label for="enrollment_date" :value="__('Enrollment Date')" />
                            <x-text-input id="enrollment_date" class="block mt-1 w-full" type="date" name="enrollment_date" :value="old('enrollment_date', $student->enrollment_date ? \Carbon\Carbon::parse($student->enrollment_date)->format('Y-m-d') : '')" required />
                            <x-input-error :messages="$errors->get('enrollment_date')" class="mt-2" />
                        </div>

                        <!-- Program -->
                        <div class="mt-4">
                            <x-input-label for="program_id" :value="__('Program')" />
                            <x-select-input id="program_id" class="block mt-1 w-full" name="program_id" required>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}" @selected(old('program_id', $student->program_id) == $program->id)>{{ $program->name }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
                        </div>

                        <!-- Major -->
                        <div class="mt-4">
                            <x-input-label for="major_id" :value="__('Major')" />
                            <x-select-input id="major_id" class="block mt-1 w-full" name="major_id" required>
                                @foreach ($majors as $major)
                                    <option value="{{ $major->id }}" @selected(old('major_id', $student->major_id) == $major->id)>{{ $major->name }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('major_id')" class="mt-2" />
                        </div>

                        <!-- Academic Year -->
                        <div class="mt-4">
                            <x-input-label for="academic_year_id" :value="__('Academic Year')" />
                            <x-select-input id="academic_year_id" class="block mt-1 w-full" name="academic_year_id" required>
                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}" @selected(old('academic_year_id', $student->academic_year_id) == $academicYear->id)>{{ $academicYear->year }}</option>
                                @endforeach
                            </x-select-input>
                            <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
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
