<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function findById(int $id): ?Order
    {
        return Order::with(['items.product', 'user', 'payment'])->find($id);
    }

    public function findByUserId(int $userId, ?int $page = null, ?int $perPage = null): LengthAwarePaginator|Collection
    {
        $query = Order::with(['items.product', 'user', 'payment'])
            ->where('user_id', $userId)
            ->latest();

        if ($page !== null && $perPage !== null) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        return $query->get();
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): bool
    {
        return $order->update($data);
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }
}

