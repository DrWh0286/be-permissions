<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class Identifier
{
    private string $identifier;

    public function __construct(string $identifier)
    {
        if (preg_match('/\s/', $identifier)) {
            throw new InvalidIdentifierException('Spaces are not allowed within an identifier string!');
        }
        $this->identifier = $identifier;
    }

    public function __toString(): string
    {
        return $this->identifier;
    }
}
