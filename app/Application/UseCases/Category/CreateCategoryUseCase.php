<?php

namespace App\Application\UseCases\Category;

use App\Domain\Exceptions\CategoryNotFoundException;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Models\Category;

class CreateCategoryUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * Create a new category.
     *
     * @param array{name: string, slug: string, description?: string, parent_id?: int} $data
     * @return Category
     * @throws CategoryNotFoundException
     */
    public function execute(array $data): Category
    {
        if (isset($data['parent_id'])) {
            $parent = $this->categoryRepository->findById($data['parent_id']);

            if (!$parent) {
                throw new CategoryNotFoundException((string) $data['parent_id'], 'id');
            }
        }

        return $this->categoryRepository->create($data);
    }
}

