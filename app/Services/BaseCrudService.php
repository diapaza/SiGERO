<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

readonly class BaseCrudService
{
    public function __construct(
        private Model $model,
    ) {}

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $entity, array $data): Model
    {
        $entity->update($data);

        return $entity->fresh();
    }

    public function delete(Model $entity): bool
    {
        if ($this->hasDependents($entity)) {
            return false;
        }

        return $entity->delete();
    }

    public function restore(Model $entity): bool
    {
        return $entity->restore();
    }

    protected function hasDependents(Model $entity): bool
    {
        return false;
    }
}
