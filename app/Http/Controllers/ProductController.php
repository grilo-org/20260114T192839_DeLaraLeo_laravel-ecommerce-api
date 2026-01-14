<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Product\CreateProductUseCase;
use App\Application\UseCases\Product\DeleteProductUseCase;
use App\Application\UseCases\Product\GetProductUseCase;
use App\Application\UseCases\Product\ListProductsUseCase;
use App\Application\UseCases\Product\UpdateProductUseCase;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private ListProductsUseCase $listProductsUseCase,
        private GetProductUseCase $getProductUseCase,
        private CreateProductUseCase $createProductUseCase,
        private UpdateProductUseCase $updateProductUseCase,
        private DeleteProductUseCase $deleteProductUseCase
    ) {
    }

    /**
     * Display a listing of products.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $filters = request()->only(['category', 'min_price', 'max_price', 'search', 'sort', 'page', 'per_page']);
        $products = $this->listProductsUseCase->execute($filters);

        return ProductResource::collection($products);
    }

    /**
     * Display the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $product = $this->getProductUseCase->execute($id);

        return response()->json(
            new ProductResource($product),
            200
        );
    }

    /**
     * Store a newly created product.
     *
     * @param StoreProductRequest $request
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $product = $this->createProductUseCase->execute($data);

        return response()->json(
            new ProductResource($product),
            201
        );
    }

    /**
     * Update the specified product.
     *
     * @param UpdateProductRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $product = $this->updateProductUseCase->execute($id, $data);

        return response()->json(
            new ProductResource($product),
            200
        );
    }

    /**
     * Remove the specified product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteProductUseCase->execute($id);

        return response()->json([
            'message' => 'Product deleted successfully.',
        ], 200);
    }
}

