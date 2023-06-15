<?php

namespace App\GraphQL\Mutations;

use Throwable;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class Logout
{
    public function __invoke(mixed $root, array $args, GraphQLContext $ctx): bool
    {
        try {
            $user = $ctx->user();

            $user->currentAccessToken()->delete();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
