<?php

namespace App\Application\UseCases;

use App\Domain\Events\PasswordResetRequested;
use App\Domain\Repositories\PasswordResetTokenRepositoryInterface;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class RequestPasswordResetUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetTokenRepositoryInterface $tokenRepository
    ) {
    }

    /**
     * Request a password reset token for a user.
     *
     * @param string $email
     * @return string Always returns Password::RESET_LINK_SENT (security: prevents email enumeration)
     */
    public function execute(string $email): string
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return Password::RESET_LINK_SENT;
        }

        // Check if a password reset was requested recently (within last 5 minutes)
        $lastRequestTime = $this->tokenRepository->getLastRequestTime($user->email);
        if ($lastRequestTime && $lastRequestTime->isAfter(now()->subMinutes(5))) {
            // Return success message even if rate limited to prevent email enumeration
            return Password::RESET_LINK_SENT;
        }

        $token = Str::random(64);
        $hashedToken = Hash::make($token);

        $this->tokenRepository->delete($user->email);
        $this->tokenRepository->create($user->email, $hashedToken);

        Event::dispatch(new PasswordResetRequested($user, $token));

        return Password::RESET_LINK_SENT;
    }
}

