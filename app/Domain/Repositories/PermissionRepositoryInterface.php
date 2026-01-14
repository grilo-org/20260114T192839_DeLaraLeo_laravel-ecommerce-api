<?php

namespace App\Domain\Repositories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

interface PermissionRepositoryInterface
{
    public function findById(int $id): ?Permission;

    public function findBySlug(string $slug): ?Permission;

    public function create(array $data): Permission;

    public function getAll(): Collection;
}

