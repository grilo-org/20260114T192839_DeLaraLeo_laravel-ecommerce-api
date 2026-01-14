<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    public function findBySlug(string $slug): ?Product
    {
        return Product::with('category')->where('slug', $slug)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): bool
    {
        return $product->update($data);
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function getAll(): Collection
    {
        return Product::with('category')->get();
    }

    /**
     * Get products with filters, pagination and sorting.
     *
     * @param array{category?: int, min_price?: float, max_price?: float, search?: string, sort?: string, page?: int, per_page?: int} $filters
     * @return LengthAwarePaginator
     */
    public function findByFilters(array $filters): LengthAwarePaginator
    {
        $query = Product::with('category');

        // Filter by category
        if (isset($filters['category'])) {
            $query->byCategory((int) $filters['category']);
        }

        // Filter by price range
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $minPrice = isset($filters['min_price']) ? (float) $filters['min_price'] : null;
            $maxPrice = isset($filters['max_price']) ? (float) $filters['max_price'] : null;
            $query->byPriceRange($minPrice, $maxPrice);
        }

        // Search by name or description
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Sorting
        if (isset($filters['sort']) && !empty($filters['sort'])) {
            $sortFields = explode(',', $filters['sort']);
            foreach ($sortFields as $field) {
                $field = trim($field);
                if (str_starts_with($field, '-')) {
                    $fieldName = substr($field, 1);
                    $query->orderBy($fieldName, 'desc');
                } else {
                    $query->orderBy($field, 'asc');
                }
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $filters['per_page'] ?? 15;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}

