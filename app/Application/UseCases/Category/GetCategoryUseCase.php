<?php

namespace App\Application\UseCases\Category;

use App\Domain\Exceptions\CategoryNotFoundException;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Models\Category;

class GetCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * Get a category by ID.
     *
     * @param int $id
     * @return Category
     * @throws CategoryNotFoundException
     */
    public function execute(int $id): Category
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new CategoryNotFoundException((string) $id, 'id');
        }

        return $category;
    }
}

