<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseFormRequest;

class StoreRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:roles,nombre'],
        ];
    }
}
