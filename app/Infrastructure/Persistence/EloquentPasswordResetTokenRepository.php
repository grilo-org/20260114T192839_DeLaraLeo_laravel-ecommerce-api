<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Repositories\PasswordResetTokenRepositoryInterface;
use App\Models\PasswordResetToken;
use Carbon\Carbon;

class EloquentPasswordResetTokenRepository implements PasswordResetTokenRepositoryInterface
{
    public function create(string $email, string $hashedToken): void
    {
        PasswordResetToken::updateOrCreate(
            ['email' => $email],
            [
                'token' => $hashedToken,
                'created_at' => now(),
            ]
        );
    }

    public function find(string $email): ?PasswordResetToken
    {
        return PasswordResetToken::where('email', $email)->first();
    }

    public function delete(string $email): void
    {
        PasswordResetToken::where('email', $email)->delete();
    }

    public function exists(string $email): bool
    {
        return PasswordResetToken::where('email', $email)->exists();
    }

    public function getLastRequestTime(string $email): ?Carbon
    {
        $token = PasswordResetToken::where('email', $email)
            ->orderBy('created_at', 'desc')
            ->first();

        return $token?->created_at;
    }

    public function deleteExpired(Carbon $expirationDate): int
    {
        return PasswordResetToken::where('created_at', '<', $expirationDate)->delete();
    }
}

