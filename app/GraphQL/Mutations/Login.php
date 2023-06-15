<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Traits\CreatesUserCredential;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Config\Repository as Config;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

final class Login
{
    use CreatesUserCredential;

    public function __construct(
        protected User $user,
        protected Hasher $hash,
        protected Config $config,
        protected Translator $translator,
    ) {
    }

    public function __invoke(mixed $root, array $args, GraphQLContext $ctx): array
    {
        $user = $this->user->newQuery()
            ->where('email', $args['email'])
            ->first();

        if (!$user || !$this->hash->check($args['password'], $user->password)) {
            throw new AuthenticationException(
                $this->translator->get('auth.failed'),
                $this->config->get('lighthouse.guards'),
            );
        }

        return $this->createUserCredential($user, $ctx->request()?->userAgent());
    }
}
