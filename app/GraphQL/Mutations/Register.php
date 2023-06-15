<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Error\Error;
use App\Traits\CreatesUserCredential;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class Register
{
    use CreatesUserCredential;

    public function __invoke(mixed $root, array $args, GraphQLContext $ctx): array
    {
        $user = new User([
            'name'     => $args['name'],
            'email'    => $args['email'],
            'password' => $args['password'],
        ]);

        if (!$user->save()) {
            throw new Error('Could not create user.');
        }

        return $this->createUserCredential($user, $ctx->request()?->userAgent());
    }
}
