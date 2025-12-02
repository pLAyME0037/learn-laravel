<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Get the student being updated to handle "unique" ignores
        $student = $this->route('student'); 
        $userId = $student->user_id;

        return [
            'name'      => ['required', 'string', 'max:255'],
            // Ignore current user for unique email/username
            'email'     => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'username'  => ['required', 'string', Rule::unique('users')->ignore($userId)],
            
            // Password is optional on update
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],

            // Student IDs usually cannot be changed, so we might remove those rules or keep them as readonly
            'department_id' => ['required', 'integer', Rule::exists('departments', 'id')],
            'program_id'    => ['required', 'integer', Rule::exists('programs', 'id')],
            
            // ... Copy other rules from StoreStudentRequest ...
            
            'date_of_birth' => ['required', 'date'],
            'gender_id'     => ['required', 'exists:genders,id'],
            'nationality'   => ['required', 'string'],
            'admission_date' => ['required', 'date'],
            
            // Contact unique check needs to ignore the current contact record
            'contact_detail.phone_number' => [
                'required', 'string', 
                Rule::unique('contact_details', 'phone_number')->ignore($student->contactDetail->id ?? null)
            ],
            
            // Allow booleans to pass through
            'has_outstanding_balance' => ['boolean'],
            'has_disability' => ['boolean'],
            // ... Add the rest of your rules
        ];
    }

    protected function prepareForValidation(): void
    {
        // Handle checkbox logic here, NOT in the service
        $this->merge([
            'has_outstanding_balance' => $this->has('has_outstanding_balance'), // Returns true/false
            'has_disability'          => $this->has('has_disability'),
        ]);
    }
}