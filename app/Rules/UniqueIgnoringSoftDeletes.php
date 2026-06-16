<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UniqueIgnoringSoftDeletes implements ValidationRule
{
    public function __construct(
        private readonly string $table,
        private readonly string $column,
        private readonly int|string|null $ignoreId = null,
    ) {}

    public static function for(string $table, string $column, int|string|null $ignoreId = null): static
    {
        return new static($table, $column, $ignoreId);
    }

    public static function forModel(Model $model, string $column, int|string|null $ignoreId = null): static
    {
        return new static($model->getTable(), $column, $ignoreId);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table($this->table)
            ->where($this->column, $value);

        if ($this->ignoreId !== null) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('validation.unique')->translate();
        }
    }
}
