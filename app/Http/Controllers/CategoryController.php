<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Category\CreateCategoryUseCase;
use App\Application\UseCases\Category\DeleteCategoryUseCase;
use App\Application\UseCases\Category\GetCategoryUseCase;
use App\Application\UseCases\Category\ListCategoriesUseCase;
use App\Application\UseCases\Category\UpdateCategoryUseCase;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(
        private ListCategoriesUseCase $listCategoriesUseCase,
        private GetCategoryUseCase $getCategoryUseCase,
        private CreateCategoryUseCase $createCategoryUseCase,
        private UpdateCategoryUseCase $updateCategoryUseCase,
        private DeleteCategoryUseCase $deleteCategoryUseCase
    ) {
    }

    /**
     * Display a listing of categories.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = $this->listCategoriesUseCase->execute();

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->getCategoryUseCase->execute($id);

        return response()->json(
            new CategoryResource($category),
            200
        );
    }

    /**
     * Store a newly created category.
     *
     * @param StoreCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $category = $this->createCategoryUseCase->execute($data);

        return response()->json(
            new CategoryResource($category),
            201
        );
    }

    /**
     * Update the specified category.
     *
     * @param UpdateCategoryRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $category = $this->updateCategoryUseCase->execute($id, $data);

        return response()->json(
            new CategoryResource($category),
            200
        );
    }

    /**
     * Remove the specified category.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteCategoryUseCase->execute($id);

        return response()->json([
            'message' => 'Category deleted successfully.',
        ], 200);
    }
}

