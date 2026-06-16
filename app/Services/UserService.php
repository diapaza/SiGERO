<?php

namespace App\Services;

use App\Models\User;

readonly class UserService
{
    public function __construct(
        private User $model,
    ) {}

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function delete(User $user): bool
    {
        if ($user->movimientos()->count() > 0) {
            return false;
        }

        return $user->delete();
    }

    public function restore(User $user): bool
    {
        return $user->restore();
    }
}
