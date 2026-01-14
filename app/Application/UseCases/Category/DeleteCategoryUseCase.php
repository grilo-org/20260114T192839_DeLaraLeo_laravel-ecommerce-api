<?php

namespace App\Application\UseCases\Category;

use App\Domain\Exceptions\CategoryNotFoundException;
use App\Domain\Repositories\CategoryRepositoryInterface;

class DeleteCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * Delete a category.
     *
     * @param int $id
     * @return void
     * @throws CategoryNotFoundException
     */
    public function execute(int $id): void
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new CategoryNotFoundException((string) $id, 'id');
        }

        $this->categoryRepository->delete($category);
    }
}
