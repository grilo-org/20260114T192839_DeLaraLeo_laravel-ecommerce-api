<?php

namespace App\Domain\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findById(int $id): ?User;

    public function updatePassword(User $user, string $password): bool;

    public function assignRole(User $user, string $roleSlug): void;

    public function removeRole(User $user, string $roleSlug): void;

    public function hasRole(User $user, string $roleSlug): bool;

    public function hasPermission(User $user, string $permissionSlug): bool;
}

