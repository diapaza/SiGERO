<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseFormRequest;
use App\Rules\UniqueIgnoringSoftDeletes;

class UpdateProfileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $user = $this->user();
        $isAdmin = $user->hasRole('Administrador');

        $rules = [
            'dni' => [
                'required',
                'string',
                'size:8',
                new UniqueIgnoringSoftDeletes('users', 'dni', $user->id),
            ],
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'whatsapp_number' => ['nullable', 'string', 'max:15'],
        ];

        if ($isAdmin) {
            $rules['username'] = [
                'required',
                'string',
                'max:255',
                new UniqueIgnoringSoftDeletes('users', 'username', $user->id),
            ];
        }

        return $rules;
    }
}
