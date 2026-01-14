<?php

namespace App\Application\Services;

use Laravel\Sanctum\NewAccessToken;

class TokenService
{
    /**
     * Create a new access token for the user.
     *
     * @param object $user
     * @param string $tokenName
     * @return NewAccessToken
     */
    public function createToken(object $user, string $tokenName = 'auth-token'): NewAccessToken
    {
        return $user->createToken($tokenName);
    }

    /**
     * Revoke all tokens for the user.
     *
     * @param object $user
     * @return void
     */
    public function revokeAllTokens(object $user): void
    {
        $user->tokens()->delete();
    }
}

