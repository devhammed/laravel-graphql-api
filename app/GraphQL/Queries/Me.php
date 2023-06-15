<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class Me
{
    public function __invoke(mixed $root, array $args, GraphQLContext $ctx): User
    {
        return $ctx->user();
    }
}
