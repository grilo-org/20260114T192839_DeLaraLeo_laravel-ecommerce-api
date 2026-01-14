<?php

namespace App\Http\Controllers;

use App\Application\UseCases\AuthenticateUserUseCase;
use App\Application\UseCases\RegisterUserUseCase;
use App\Application\UseCases\RequestPasswordResetUseCase;
use App\Application\UseCases\ResetPasswordUseCase;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(
        private RegisterUserUseCase $registerUserUseCase,
        private AuthenticateUserUseCase $authenticateUserUseCase,
        private RequestPasswordResetUseCase $requestPasswordResetUseCase,
        private ResetPasswordUseCase $resetPasswordUseCase
    ) {
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->registerUserUseCase->execute($data);

        return response()->json(
            new AuthResource($result),
            201
        );
    }

    /**
     * Authenticate user and return token.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $result = $this->authenticateUserUseCase->execute(
            $credentials['email'],
            $credentials['password']
        );

        if (!$result) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        return response()->json(
            new AuthResource($result),
            200
        );
    }

    /**
     * Request password reset.
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->requestPasswordResetUseCase->execute($request->validated()['email']);

        return response()->json([
            'message' => 'If the email exists, a password reset link has been sent.',
        ], 200);
    }

    /**
     * Reset password.
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->resetPasswordUseCase->execute($request->validated());

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password has been reset successfully.',
            ], 200);
        }

        return response()->json([
            'message' => 'Unable to reset password. Please check your token and try again.',
        ], 400);
    }

    /**
     * Get the authenticated user.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();
        $user->load('roles');

        return response()->json(
            new UserResource($user),
            200
        );
    }
}

