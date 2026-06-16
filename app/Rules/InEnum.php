<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

class InEnum implements ValidationRule
{
    public function __construct(
        private readonly string $enumClass,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        (new Enum($this->enumClass))->validate($attribute, $value, $fail);
    }
}
