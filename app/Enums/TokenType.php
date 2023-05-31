<?php

namespace App\Enums;

use GraphQL\Type\Definition\Description;

#[Description('The type of token to be generated')]
enum TokenType: string
{
    #[Description('A token that can be used to authenticate requests using Authorization header.')]
    case BEARER = 'Bearer';
}
