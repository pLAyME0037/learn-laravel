<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                                      => ['required', 'string', 'max:255'],
            'email'                                     => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'username'                                  => ['required', 'string', 'max:255', Rule::unique('users', 'username')],
            'department_id'                             => ['required', 'integer', Rule::exists('departments', 'id')],
            'program_id'                                => ['required', 'integer', Rule::exists('programs', 'id')],
            'year_level'                                => ['nullable', 'integer', 'min:1', 'max:4'],
            'student_id'                                => ['nullable', 'string', 'max:255', Rule::unique('students', 'student_id')],
            'registration_number'                       => ['nullable', 'string', 'max:255', Rule::unique('students', 'registration_number')],
            'date_of_birth'                             => ['required', 'date'],
            'gender_id'                                 => ['required', 'integer', Rule::exists('genders', 'id')],
            'nationality'                               => ['required', 'string', 'max:255'],
            'id_card_number'                            => ['nullable', 'string', 'max:255', Rule::unique('students', 'id_card_number')],
            'passport_number'                           => ['nullable', 'string', 'max:255', Rule::unique('students', 'passport_number')],
            'admission_date'                            => ['required', 'date'],
            'expected_graduation'                       => ['required', 'date', 'after:admission_date'],
            'semester'                                  => ['required', 'string', 'max:255'],
            'current_semester'                          => ['nullable', 'integer', 'min:1'],
            'cgpa'                                      => ['nullable', 'numeric', 'min:0', 'max:4'],
            'total_credits_earned'                      => ['nullable', 'integer', 'min:0'],
            'academic_status'                           => ['required', 'string', Rule::in(['active', 'graduated', 'suspended', 'probation'])],
            'enrollment_status'                         => ['required', 'string', Rule::in(['full_time', 'part_time', 'exchange', 'study_abroad'])],
            'fee_category'                              => ['required', 'string', Rule::in(['regular', 'scholarship', 'financial_aid', 'self_financed'])],
            'has_outstanding_balance'                   => ['boolean'],
            'previous_education'                        => ['nullable', 'string'],
            'blood_group'                               => ['nullable', 'string', 'max:5'],
            'has_disability'                            => ['boolean'], // Not required, handled by preprocessing
            'disability_details'                        => ['nullable', 'string', 'required_if:has_disability,true'],
            'metadata'                                  => ['nullable', 'array'],

            // Password for user creation
            'password'                                  => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation'                     => ['required', 'string', 'min:8'], // Explicitly added for 'confirmed' rule

            // Contact Detail (nested)
            'contact_detail.phone_number'               => ['required', 'string', 'max:20', Rule::unique('contact_details', 'phone_number')],
            'contact_detail.emergency_contact_name'     => ['nullable', 'string', 'max:255'],
            'contact_detail.emergency_contact_phone'    => ['nullable', 'string', 'max:20'],
            'contact_detail.emergency_contact_relation' => ['nullable', 'string', 'max:255'],

            // Address (nested)
            'address.current_address'                   => ['required', 'string'], // Made required based on blade
            'address.postal_code'                       => ['required', 'string', 'max:255'], // Made required based on blade
            'address.village_id'                        => ['required', 'integer', Rule::exists('villages', 'id')],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_outstanding_balance' => $this->has('has_outstanding_balance') && $this->input('has_outstanding_balance') === 'on',
            'has_disability'          => $this->has('has_disability') && $this->input('has_disability') === 'on',
            // Ensure year_level defaults if not provided, aligning with model's boot method
            'year_level'              => $this->input('year_level', 1),
            // Ensure current_semester defaults if not provided
            'current_semester'        => $this->input('current_semester', 1),
            // Ensure semester defaults if not provided
            'semester'                => $this->input('semester', 'semester_one'),
        ]);
    }

    // Optional: Customize error messages for nicer UI
    public function attributes(): array
    {
        return [
            'address.village_id'          => 'village', // Custom attribute for village_id
            'contact_detail.phone_number' => 'phone number',
        ];
    }
}
