<?php

namespace App\Domain\Repositories;

use App\Models\PasswordResetToken;

interface PasswordResetTokenRepositoryInterface
{
    public function create(string $email, string $hashedToken): void;

    public function find(string $email): ?PasswordResetToken;

    public function delete(string $email): void;

    public function exists(string $email): bool;

    /**
     * Get the timestamp of the last password reset request for an email.
     *
     * @param string $email
     * @return \Carbon\Carbon|null
     */
    public function getLastRequestTime(string $email): ?\Carbon\Carbon;

    /**
     * Delete expired tokens older than the given date.
     *
     * @param \Carbon\Carbon $expirationDate
     * @return int Number of deleted tokens
     */
    public function deleteExpired(\Carbon\Carbon $expirationDate): int;
}

