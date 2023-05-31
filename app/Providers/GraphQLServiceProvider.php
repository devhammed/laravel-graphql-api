<?php

namespace App\Providers;

use App\Enums\TokenType;
use Illuminate\Support\ServiceProvider;
use GraphQL\Type\Definition\PhpEnumType;
use Nuwave\Lighthouse\Schema\TypeRegistry;

class GraphQLServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app
            ->make(TypeRegistry::class)
            ->register(new PhpEnumType(TokenType::class));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
