<?php

namespace App\Application\UseCases\Product;

use App\Domain\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProductsUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * List products with filters, pagination and sorting.
     *
     * @param array{category?: int, min_price?: float, max_price?: float, search?: string, sort?: string, page?: int, per_page?: int} $filters
     * @return LengthAwarePaginator
     */
    public function execute(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->findByFilters($filters);
    }
}

