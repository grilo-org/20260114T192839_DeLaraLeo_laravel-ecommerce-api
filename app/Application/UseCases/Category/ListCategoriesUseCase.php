<?php

namespace App\Application\UseCases\Category;

use App\Domain\Repositories\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ListCategoriesUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * List all categories.
     *
     * @return Collection
     */
    public function execute(): Collection
    {
        return $this->categoryRepository->getAll();
    }
}

