<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    /**
     * Health check endpoint
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => config('app.name', 'boilerplate'),
            'timestamp' => now()->toIso8601String(),
            'environment' => config('app.env'),
        ]);
    }
}

