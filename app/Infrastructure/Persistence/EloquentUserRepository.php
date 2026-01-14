<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\RoleRepositoryInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function updatePassword(User $user, string $password): bool
    {
        $user->password = Hash::make($password);
        return $user->save();
    }

    public function assignRole(User $user, string $roleSlug): void
    {
        $role = $this->roleRepository->findBySlug($roleSlug);
        
        if ($role && !$user->hasRole($roleSlug)) {
            $user->assignRoleById($role->id);
        }
    }

    public function removeRole(User $user, string $roleSlug): void
    {
        $role = $this->roleRepository->findBySlug($roleSlug);
        
        if ($role) {
            $user->removeRoleById($role->id);
        }
    }

    public function hasRole(User $user, string $roleSlug): bool
    {
        return $user->hasRole($roleSlug);
    }

    public function hasPermission(User $user, string $permissionSlug): bool
    {
        return $user->hasPermission($permissionSlug);
    }
}


