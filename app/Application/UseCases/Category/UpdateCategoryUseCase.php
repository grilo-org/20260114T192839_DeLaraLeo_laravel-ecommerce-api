<?php

namespace App\Application\UseCases\Category;

use App\Domain\Exceptions\CategoryNotFoundException;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Models\Category;

class UpdateCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * Update an existing category.
     *
     * @param int $id
     * @param array{name?: string, slug?: string, description?: string, parent_id?: int} $data
     * @return Category
     * @throws CategoryNotFoundException
     */
    public function execute(int $id, array $data): Category
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new CategoryNotFoundException((string) $id, 'id');
        }

        if (isset($data['parent_id'])) {
            $parent = $this->categoryRepository->findById($data['parent_id']);

            if (!$parent) {
                throw new CategoryNotFoundException((string) $data['parent_id'], 'id');
            }

            // Prevent category from being its own parent
            if ($data['parent_id'] === $id) {
                throw new CategoryNotFoundException('Category cannot be its own parent.', 'id');
            }
        }

        $this->categoryRepository->update($category, $data);

        return $category->fresh();
    }
}

