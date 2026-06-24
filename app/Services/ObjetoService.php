<?php

namespace App\Services;

use App\Models\Objeto;

readonly class ObjetoService
{
    public function __construct(
        private Objeto $model,
    ) {}

    public function create(array $data): Objeto
    {
        $objeto = $this->model->create($data);

        if ($objeto->foto) {
            $newPath = app(ImageService::class)->renameImage($objeto->foto, $objeto->codigo);
            $objeto->update(['foto' => $newPath]);
        }

        return $objeto;
    }

    public function update(Objeto $objeto, array $data): Objeto
    {
        $objeto->update($data);

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

    public function delete(Objeto $objeto): bool
    {
        if ($objeto->movimientos()->count() > 0) {
            return false;
        }

        if ($objeto->foto) {
            app(ImageService::class)->delete($objeto->foto);
        }

        return $objeto->delete();
    }

    public function restore(Objeto $objeto): bool
    {
        return $objeto->restore();
    }
}
