<?php

namespace App\Application\UseCases\Product;

use App\Domain\Exceptions\CategoryNotFoundException;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Models\Product;

class CreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * Create a new product.
     *
     * @param array{name: string, slug: string, description?: string, price: float, stock: int, category_id: int, image_url?: string} $data
     * @return Product
     * @throws CategoryNotFoundException
     */
    public function execute(array $data): Product
    {
        $category = $this->categoryRepository->findById($data['category_id']);

        if (!$category) {
            throw new CategoryNotFoundException((string) $data['category_id'], 'id');
        }

        return $this->productRepository->create($data);
    }
}

