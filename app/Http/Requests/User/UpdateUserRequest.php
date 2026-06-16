<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

class UpdateUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'username' => [
                'required',
                'string',
                'max:255',
                new UniqueIgnoringSoftDeletes('users', 'username', $user?->id),
            ],
            'dni' => [
                'required',
                'string',
                'size:8',
                new UniqueIgnoringSoftDeletes('users', 'dni', $user?->id),
            ],
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'whatsapp_number' => ['nullable', 'string', 'max:15'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
