<?php

namespace App\GraphQL\Mutations;

use App\Models\File;
use Exception;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class Upload
{
    public function __invoke(mixed $root, array $args, GraphQLContext $ctx): File
    {
        return DB::transaction(function () use ($ctx, $args) {
            $user = $ctx->user();

            $oldPhoto = $user->photo;

            $photo = File::fromUploadedFile($args['file']);

            if (!($photo = $user->photo()->save($photo))) {
                throw new Exception('Failed to upload photo.');
            }

            $oldPhoto?->delete();

            return $photo;
        });
    }
}
