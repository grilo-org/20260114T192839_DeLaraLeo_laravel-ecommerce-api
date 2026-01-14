<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Order\CancelOrderUseCase;
use App\Application\UseCases\Order\CreateOrderUseCase;
use App\Application\UseCases\Order\GetOrderUseCase;
use App\Application\UseCases\Order\ListOrdersUseCase;
use App\Application\UseCases\Order\UpdateOrderStatusUseCase;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\Order\OrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        private CreateOrderUseCase $createOrderUseCase,
        private ListOrdersUseCase $listOrdersUseCase,
        private GetOrderUseCase $getOrderUseCase,
        private UpdateOrderStatusUseCase $updateOrderStatusUseCase,
        private CancelOrderUseCase $cancelOrderUseCase
    ) {
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->createOrderUseCase->execute(auth()->id());

        return response()->json(
            new OrderResource($order),
            201
        );
    }

    public function index(): AnonymousResourceCollection
    {
        $orders = $this->listOrdersUseCase->execute(auth()->id(), request('page'), request('per_page'));

        return OrderResource::collection($orders);
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->getOrderUseCase->execute($id, auth()->id());

        return response()->json(
            new OrderResource($order),
            200
        );
    }

    public function updateStatus(int $id, UpdateOrderStatusRequest $request): JsonResponse
    {
        $order = $this->updateOrderStatusUseCase->execute($id, auth()->id(), $request->validated()['status']);

        return response()->json(
            new OrderResource($order),
            200
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $order = $this->cancelOrderUseCase->execute($id, auth()->id());

        return response()->json(
            new OrderResource($order),
            200
        );
    }
}

