<?php

namespace App\Application\UseCases;

use App\Application\Services\TokenService;
use App\Domain\Events\UserRegistered;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenService $tokenService
    ) {
    }

    /**
     * Register a new user and generate authentication token.
     *
     * @param array{name: string, email: string, password: string, password_confirmation: string} $data
     * @return array{user: object, token: string}
     */
    public function execute(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        $user = $this->userRepository->create($data);

        $token = $this->tokenService->createToken($user);

        Event::dispatch(new UserRegistered($user));

        return [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];
    }
}

