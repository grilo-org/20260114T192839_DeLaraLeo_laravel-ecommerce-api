<?php

namespace App\Application\UseCases;

use App\Application\Services\TokenService;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthenticateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenService $tokenService
    ) {
    }

    /**
     * Authenticate user and generate token.
     *
     * @param string $email
     * @param string $password
     * @return array{user: object, token: string}|null Returns null if credentials are invalid
     */
    public function execute(string $email, string $password): ?array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        $token = $this->tokenService->createToken($user);

        return [
            'user' => $user,
            'token' => $token->plainTextToken,
        ];
    }
}

