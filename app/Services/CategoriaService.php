<?php

namespace App\Services;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Model;

readonly class CategoriaService extends BaseCrudService
{
    public function __construct(
        private Categoria $model,
    ) {
        parent::__construct($model);
    }

    protected function hasDependents(Model $entity): bool
    {
        /** @var Categoria $categoria */
        $categoria = $entity;

        return $categoria->objetos()->count() > 0;
    }
}
