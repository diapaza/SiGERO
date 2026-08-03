<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseFormRequest;

class UpdateRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name,' . $role?->id,
            ],
            'guard_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
