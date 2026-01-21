<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        $targetuser = this->route("user");
        return this->user()->can("edit.users")
            && this->user()->can("changePass", $targetuser);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        /**
         * $var User $user
         */
        $user = $this->route("user");
        return [
            'name'      => ['required', 'string', 'max:255'],
            'username'  => [ 'required', 'string', 'max:255', 'alpha_dash',
                'unique:users,username,' . $user->id
            ],
            'email'     => [ 'required', 'string', 'email', 'max:255',
                'unique:users,email,' . $user->id
            ],
            'bio'       => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
            'password'  => ['nullable', 'confirmed', Password::min(8)],
        ];
    }
}
