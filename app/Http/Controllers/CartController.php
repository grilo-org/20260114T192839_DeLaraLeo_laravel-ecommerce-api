<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Cart\AddToCartUseCase;
use App\Application\UseCases\Cart\ClearCartUseCase;
use App\Application\UseCases\Cart\GetCartUseCase;
use App\Application\UseCases\Cart\RemoveFromCartUseCase;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Resources\Cart\CartResource;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(
        private GetCartUseCase $getCartUseCase,
        private AddToCartUseCase $addToCartUseCase,
        private RemoveFromCartUseCase $removeFromCartUseCase,
        private ClearCartUseCase $clearCartUseCase
    ) {
    }

    public function index(): JsonResponse
    {
        $cart = $this->getCartUseCase->execute(auth()->id());

        return response()->json(
            new CartResource($cart),
            200
        );
    }

    public function add(AddToCartRequest $request): JsonResponse
    {
        $data = $request->validated();
        $cart = $this->addToCartUseCase->execute(auth()->id(), $data);

        return response()->json(
            new CartResource($cart),
            200
        );
    }

    public function remove(int $id): JsonResponse
    {
        $cart = $this->removeFromCartUseCase->execute(auth()->id(), $id);

        return response()->json(
            new CartResource($cart),
            200
        );
    }

    public function clear(): JsonResponse
    {
        $cart = $this->clearCartUseCase->execute(auth()->id());

        return response()->json(
            new CartResource($cart),
            200
        );
    }
}

