<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Error\Error;
use App\Traits\CreatesUserCredential;

final class Register
{
    use CreatesUserCredential;

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

        return $this->createUserCredential(
            $this->user,
            $args['token_name'] ?? 'default',
        );
    }
}
