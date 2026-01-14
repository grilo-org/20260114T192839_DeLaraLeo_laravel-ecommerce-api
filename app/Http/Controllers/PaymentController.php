<?php

namespace App\Http\Controllers;

use App\Application\UseCases\Payment\GetPaymentUseCase;
use App\Application\UseCases\Payment\ProcessPaymentUseCase;
use App\Http\Requests\Payment\ProcessPaymentRequest;
use App\Http\Resources\Payment\PaymentResource;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(
        private ProcessPaymentUseCase $processPaymentUseCase,
        private GetPaymentUseCase $getPaymentUseCase
    ) {
    }

    public function process(int $orderId, ProcessPaymentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $payment = $this->processPaymentUseCase->execute($orderId, auth()->id(), $data);

        return response()->json(
            new PaymentResource($payment),
            201
        );
    }

    public function show(int $orderId): JsonResponse
    {
        $payment = $this->getPaymentUseCase->execute($orderId, auth()->id());

        return response()->json(
            new PaymentResource($payment),
            200
        );
    }
}

