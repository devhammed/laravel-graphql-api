<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use App\Enums\TokenType;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Config\Repository as Config;
use Nuwave\Lighthouse\Exceptions\AuthenticationException;

final class Login
{
    public function __construct(
        protected User $user,
        protected Hasher $hash,
        protected Config $config,
        protected Translator $translator,
    ) {
    }

    public function __invoke(mixed $root, array $args): array
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

        $token = $user->createToken($args['token_name'] ?? 'default');

        return [
            'user'         => $user,
            'access_token' => [
                'name'       => $token->accessToken->name,
                'type'       => TokenType::BEARER,
                'value'      => $token->plainTextToken,
                'abilities'  => $token->accessToken->abilities,
                'expires_at' => $token->accessToken->expires_at,
                'created_at' => $token->accessToken->created_at,
            ],
        ];
    }
}
