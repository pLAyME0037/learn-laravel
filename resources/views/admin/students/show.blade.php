<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <x-input-label for="user_name" :value="__('User Name')" />
                        <p>{{ $student->user->name }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="student_id" :value="__('Student ID')" />
                        <p>{{ $student->student_id }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                        <p>{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d-m-Y') : '' }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="gender" :value="__('Gender')" />
                        <p>{{ $student->gender->name }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="address" :value="__('Address')" />
                        <p>{{ $student->address }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="phone_number" :value="__('Phone Number')" />
                        <p>{{ $student->phone_number }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="enrollment_date" :value="__('Enrollment Date')" />
                        <p>{{ $student->enrollment_date ? \Carbon\Carbon::parse($student->enrollment_date)->format('d-m-Y') : '' }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="program" :value="__('Program')" />
                        <p>{{ $student->program->name }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="major" :value="__('Major')" />
                        <p>{{ $student->major->name }}</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="academic_year" :value="__('Academic Year')" />
                        <p>{{ $student->academicYear->year }}</p>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.students.edit', $student) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Back to List') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
