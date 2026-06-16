<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class StoreUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'dni' => ['required', 'string', 'size:8', 'unique:users,dni'],
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'whatsapp_number' => ['nullable', 'string', 'max:15'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
