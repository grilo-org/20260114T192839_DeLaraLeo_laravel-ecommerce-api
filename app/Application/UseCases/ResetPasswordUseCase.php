<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetTokenRepositoryInterface $tokenRepository
    ) {
    }

    /**
     * Reset user password using a valid reset token.
     *
     * @param array{email: string, token: string, password: string} $data
     * @return string Returns Password::PASSWORD_RESET on success, Password::INVALID_USER or Password::INVALID_TOKEN on failure
     */
    public function execute(array $data): string
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user) {
            return Password::INVALID_USER;
        }

        $tokenRecord = $this->tokenRepository->find($user->email);

        if (!$tokenRecord || !Hash::check($data['token'], $tokenRecord->token)) {
            return Password::INVALID_TOKEN;
        }

        if (now()->diffInMinutes($tokenRecord->created_at) > 60) {
            $this->tokenRepository->delete($user->email);
            return Password::INVALID_TOKEN;
        }

        $this->userRepository->updatePassword($user, $data['password']);
        $this->tokenRepository->delete($user->email);

        return Password::PASSWORD_RESET;
    }
}

