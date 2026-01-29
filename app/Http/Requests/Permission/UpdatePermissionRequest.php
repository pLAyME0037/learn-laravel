<?php

declare(strict_types=1);

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can("Manage Roles & Permissions");
    }

    public function prepareForValidation(): void {
        if (! $this->input('group')) $this->merge(['group' => 'system']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        /** @var Permission $permission */
        $permission = $this->route('permission');

        return [
            'name'        => [ 'required', 'string', 'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id),
            ],
            'group'       => ['required', 'string', 'max:50'],
            'guard_name'  => ['required', 'string', 'max:125'],
            'description' => ['nullable', 'string', 'max:255'],
            'role_id'     => ['nullable', 'integer', 'exists:roles,id'],
        ];
    }
}
