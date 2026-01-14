<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\PermissionRepositoryInterface;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class EloquentPermissionRepository implements PermissionRepositoryInterface
{
    public function findById(int $id): ?Permission
    {
        return Permission::find($id);
    }

    public function findBySlug(string $slug): ?Permission
    {
        return Permission::where('slug', $slug)->first();
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function getAll(): Collection
    {
        return Permission::all();
    }
}

