<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionToRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_slug' => ['required', 'string', 'exists:roles,slug'],
            'permission_slug' => ['required', 'string', 'exists:permissions,slug'],
        ];
    }
}

