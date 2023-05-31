<?php

namespace App\GraphQL\Mutations;

use Throwable;
use Illuminate\Contracts\Auth\Guard;

final class Logout
{
    public function __construct(protected Guard $guard)
    {
    }

    public function __invoke(mixed $root, array $args): bool
    {
        try {
            $user = $this->guard->user();

            $user->currentAccessToken()->delete();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
