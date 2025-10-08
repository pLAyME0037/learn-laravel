<?php
namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'username'    => [
                'required', 
                'string', 
                'max:255', 
                'alpha_dash',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'email'       => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'profile_pic' => [
                'nullable', 
                'image', 
                'mimes:png,jpg,jpeg,gif', 
                'max:2048'
            ],
            'bio'         => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom attributes for validator error
     * @return array{profile_pic: string}
     */
    public function attributes() {
        return [
            'profile_pic' => 'profile picture',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required.',
            'username.unique'   => 'This username is already taken.',
            'profile_pic.image' => 'The profile picture must be an image.',
            'profile_pic.mimes' => 'The profile picture must be a JPEG, PNG, JPG, or GIF.',
            'profile_pic.max'   => 'The profile picture must not exceed 2MB.',
            'bio.max'           => 'Bio must not exceed 500 characters.',
        ];
    }
}
