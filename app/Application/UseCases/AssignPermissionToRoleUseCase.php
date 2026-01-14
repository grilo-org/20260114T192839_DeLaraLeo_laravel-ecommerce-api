<?php

namespace App\Application\UseCases;

use App\Domain\Exceptions\PermissionNotFoundException;
use App\Domain\Exceptions\RoleNotFoundException;
use App\Domain\Repositories\PermissionRepositoryInterface;
use App\Domain\Repositories\RoleRepositoryInterface;

class AssignPermissionToRoleUseCase
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private PermissionRepositoryInterface $permissionRepository
    ) {
    }

    /**
     * Assign a permission to a role.
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

        if (!$role->hasPermission($permissionSlug)) {
            $role->assignPermission($permission->id);
        }
    }
}

