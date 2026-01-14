<?php

namespace App\Application\UseCases;

use App\Domain\Exceptions\RoleNotFoundException;
use App\Domain\Exceptions\UserNotFoundException;
use App\Domain\Repositories\RoleRepositoryInterface;
use App\Domain\Repositories\UserRepositoryInterface;

class AssignRoleToUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    /**
     * Assign a role to a user.
     *
     * @param int $userId
     * @param string $roleSlug
     * @return void
     * @throws UserNotFoundException
     * @throws RoleNotFoundException
     */
    public function execute(int $userId, string $roleSlug): void
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException((string) $userId, 'id');
        }

        $role = $this->roleRepository->findBySlug($roleSlug);

        if (!$role) {
            throw new RoleNotFoundException($roleSlug);
        }

        $this->userRepository->assignRole($user, $roleSlug);
    }
}

