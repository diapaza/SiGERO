<?php

namespace App\Services;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Model;

readonly class MarcaService extends BaseCrudService
{
    public function __construct(
        private Marca $model,
    ) {
        parent::__construct($model);
    }

    protected function hasDependents(Model $entity): bool
    {
        /** @var Marca $marca */
        $marca = $entity;

        return $marca->objetos()->count() > 0;
    }
}
