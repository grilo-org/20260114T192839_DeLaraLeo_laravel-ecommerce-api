<?php

namespace App\Application\UseCases;

use App\Domain\Exceptions\PermissionNotFoundException;
use App\Domain\Exceptions\RoleNotFoundException;
use App\Domain\Repositories\PermissionRepositoryInterface;
use App\Domain\Repositories\RoleRepositoryInterface;

class RemovePermissionFromRoleUseCase
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private PermissionRepositoryInterface $permissionRepository
    ) {
    }

    /**
     * Remove a permission from a role.
     *
     * @param string $roleSlug
     * @param string $permissionSlug
     * @return void
     * @throws RoleNotFoundException
     * @throws PermissionNotFoundException
     */
    public function execute(string $roleSlug, string $permissionSlug): void
    {
        $role = $this->roleRepository->findBySlug($roleSlug);

        if (!$role) {
            throw new RoleNotFoundException($roleSlug);
        }

        $permission = $this->permissionRepository->findBySlug($permissionSlug);

        if (!$permission) {
            throw new PermissionNotFoundException($permissionSlug);
        }

        $role->removePermission($permission->id);
    }
}

