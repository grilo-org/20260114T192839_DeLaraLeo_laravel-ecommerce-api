<?php

namespace App\Application\UseCases\Product;

use App\Domain\Exceptions\ProductNotFoundException;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Models\Product;

class GetProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * Get a product by ID.
     *
     * @param int $id
     * @return Product
     * @throws ProductNotFoundException
     */
    public function execute(int $id): Product
    {
        $product = $this->productRepository->findById($id);

        if (!$product) {
            throw new ProductNotFoundException((string) $id, 'id');
        }

        return $product;
    }
}

