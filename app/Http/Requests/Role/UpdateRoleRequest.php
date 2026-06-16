<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

class UpdateRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                new UniqueIgnoringSoftDeletes('roles', 'nombre', $role?->id),
            ],
        ];
    }
}
