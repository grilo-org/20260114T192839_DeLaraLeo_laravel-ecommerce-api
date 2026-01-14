<?php

namespace App\Application\UseCases\Order;

use App\Domain\Repositories\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ListOrdersUseCase
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function execute(int $userId, ?int $page = null, ?int $perPage = null): LengthAwarePaginator|Collection
    {
        return $this->orderRepository->findByUserId($userId, $page, $perPage);
    }
}

