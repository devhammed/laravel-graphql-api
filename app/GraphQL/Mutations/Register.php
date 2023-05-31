<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Enums\TokenType;
use GraphQL\Error\Error;

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
            'access_token' => [
                'name'       => $token->accessToken->name,
                'type'       => TokenType::BEARER,
                'value'      => $token->plainTextToken,
                'abilities'  => $token->accessToken->abilities,
                'expires_at' => $token->accessToken->expires_at,
                'created_at' => $token->accessToken->created_at,
            ],
        ];
    }
}
