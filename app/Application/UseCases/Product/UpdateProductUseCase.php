<?php

namespace App\Application\UseCases\Product;

use App\Domain\Exceptions\CategoryNotFoundException;
use App\Domain\Exceptions\ProductNotFoundException;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Models\Product;

class UpdateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * Update an existing product.
     *
     * @param int $id
     * @param array{name?: string, slug?: string, description?: string, price?: float, stock?: int, category_id?: int, image_url?: string} $data
     * @return Product
     * @throws ProductNotFoundException
     * @throws CategoryNotFoundException
     */
    public function execute(int $id, array $data): Product
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw new ProductNotFoundException((string) $id, 'id');
        }

        if (isset($data['category_id'])) {
            $category = $this->categoryRepository->findById($data['category_id']);

            if (!$category) {
                throw new CategoryNotFoundException((string) $data['category_id'], 'id');
            }
        }

        $this->productRepository->update($product, $data);

        return $product->fresh();
    }
}

