<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
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

        $token = $this->user
            ->createToken($args['token_name'] ?? 'default')
            ->plainTextToken;

        return [
            'access_token' => $token,
            'user'         => $this->user,
        ];
    }
}
