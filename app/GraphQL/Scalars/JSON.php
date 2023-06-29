<?php

namespace App\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\Printer;
use GraphQL\Type\Definition\ScalarType;
use Safe\Exceptions\JsonException;

/**
 * Read more about scalars here https://webonyx.github.io/graphql-php/type-definitions/scalars
 */
final class JSON extends ScalarType
{
    /**
     * Serializes an internal value to include in a response.
     *
     * @throws Error
     */
    public function serialize(mixed $value): mixed
    {
        if ($this->isValidJSONValue($value)) {
            return $value;
        }

        try {
            return \Safe\json_encode($value);
        } catch (JsonException $jsonException) {
            throw new Error(
                $jsonException->getMessage()
            );
        }
    }

    /**
     * Parses an externally provided value (query variable) to use as an input.
     *
     * @throws Error
     */
    public function parseValue($value): mixed
    {
        return $this->decodeJSON($value);
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * @throws Error
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): mixed
    {
        if (! property_exists($valueNode, 'value')) {
            $withoutValue = Printer::doPrint($valueNode);
            throw new Error("Can not parse literals without a value: {$withoutValue}.");
        }

        return $this->decodeJSON($valueNode->value);
    }

    /**
     * Check if value is a JSON primitive.
     */
    protected function isValidJSONValue(mixed $value): bool
    {
        return is_string($value) ||
            is_int($value) ||
            is_float($value) ||
            is_bool($value) ||
            is_array($value) ||
            is_object($value) ||
            $value === null;
    }

    /**
     * Try to decode a user-given JSON value.
     *
     * @throws Error
     */
    protected function decodeJSON(mixed $value): mixed
    {
        try {
           return \Safe\json_decode($value);
        } catch (JsonException $jsonException) {
            if ($this->isValidJSONValue($value)) {
                return $value;
            }

            throw new Error(
                $jsonException->getMessage()
            );
        }
    }
}
