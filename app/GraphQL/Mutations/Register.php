<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Error\Error;
use App\Enums\TokenType;

final class Register
{
    public function __construct(protected User $user)
    {
    }

    public function __invoke(mixed $root, array $args): array
    {
        $saved = $this->user
            ->fill([
                'name'     => $args['name'],
                'email'    => $args['email'],
                'password' => $args['password'],
            ])
            ->save();

        if (!$saved) {
            throw new Error('Could not create user.');
        }

        $token = $this->user->createToken($args['token_name'] ?? 'default');

        return [
            'user'         => $this->user,
            'token_type'   => TokenType::BEARER,
            'access_token' => $token->plainTextToken,
            'expires_in'   => $token->accessToken->expires_at?->getTimestamp(),
        ];
    }
}
