<?php

namespace App\Domain\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;

    public function findByUserId(int $userId, ?int $page = null, ?int $perPage = null): LengthAwarePaginator|Collection;

    public function create(array $data): Order;

    public function update(Order $order, array $data): bool;

    public function delete(Order $order): bool;
}

