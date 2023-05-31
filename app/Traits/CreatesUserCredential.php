<?php

namespace App\Traits;

use App\Models\User;
use App\Enums\TokenType;

trait CreatesUserCredential
{
    /**
     * Create a credential for a user.
     */
    protected function createUserCredential(User $user, string $tokenName): array
    {
        $newAccessToken = $user->createToken($tokenName);

        return [
            'user'         => $user,
            'access_token' => [
                'type'       => TokenType::BEARER,
                'value'      => $newAccessToken->plainTextToken,
                'name'       => $newAccessToken->accessToken->name,
                'abilities'  => $newAccessToken->accessToken->abilities,
                'expires_at' => $newAccessToken->accessToken->expires_at,
                'created_at' => $newAccessToken->accessToken->created_at,
            ],
        ];
    }
}
