<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use Illuminate\Contracts\Auth\Guard;

final class Me
{
    public function __construct(protected Guard $guard)
    {
    }

    public function __invoke(mixed $root, array $args): User
    {
        return $this->guard->user();
    }
}
