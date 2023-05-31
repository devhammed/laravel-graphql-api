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
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(TypeRegistry $typeRegistry): void
    {
        $typeRegistry->register(new PhpEnumType(TokenType::class));
    }
}
