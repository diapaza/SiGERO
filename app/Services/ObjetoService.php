<?php

namespace App\Services;

use App\Models\Objeto;
use Illuminate\Database\Eloquent\Model;

readonly class ObjetoService extends BaseCrudService
{
    public function __construct(
        private Objeto $model,
    ) {
        parent::__construct($model);
    }

    public function create(array $data): Objeto
    {
        unset($data['disponible']);
        $data['disponible'] = true;

        $objeto = parent::create($data);

        if ($objeto->foto) {
            $newPath = app(ImageService::class)->renameImage($objeto->foto, $objeto->codigo);
            $objeto->update(['foto' => $newPath]);
        }

        return $objeto;
    }

    public function update(Model $entity, array $data): Model
    {
        /** @var Objeto $objeto */
        $objeto = $entity;
        unset($data['disponible']);
        parent::update($objeto, $data);
        $objeto = $objeto->fresh();

        if ($objeto->foto) {
            $currentFilename = basename($objeto->foto);
            $expectedFilename = $objeto->codigo . '.jpg';

            if ($currentFilename !== $expectedFilename) {
                $newPath = app(ImageService::class)->renameImage($objeto->foto, $objeto->codigo);
                $objeto->update(['foto' => $newPath]);
            }
        }

        return $objeto->fresh();
    }

    protected function hasDependents(Model $entity): bool
    {
        /** @var Objeto $objeto */
        $objeto = $entity;

        return $objeto->movimientos()->count() > 0;
    }
}
