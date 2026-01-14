<?php

namespace App\Application\UseCases\Product;

use App\Domain\Exceptions\ProductNotFoundException;
use App\Domain\Repositories\ProductRepositoryInterface;

class DeleteProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return void
     * @throws ProductNotFoundException
     */
    public function execute(int $id): void
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw new ProductNotFoundException((string) $id, 'id');
        }

        $this->productRepository->delete($product);
    }
}

