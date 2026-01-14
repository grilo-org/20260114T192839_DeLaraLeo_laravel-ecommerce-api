<?php

namespace App\Domain\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findBySlug(string $slug): ?Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): bool;

    public function delete(Product $product): bool;

    public function getAll(): Collection;

    /**
     * Get products with filters, pagination and sorting.
     *
     * @param array{category?: int, min_price?: float, max_price?: float, search?: string, sort?: string, page?: int, per_page?: int} $filters
     * @return LengthAwarePaginator
     */
    public function findByFilters(array $filters): LengthAwarePaginator;
}

