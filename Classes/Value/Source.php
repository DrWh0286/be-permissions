<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

final class Source implements \Stringable
{
    private const ALLOWED_SOURCES = [
        'production',
        'staging',
        'testing'
    ];
    private string $source;

    public function __construct(string $source = 'staging')
    {
        if (!in_array($source, self::ALLOWED_SOURCES)) {
            throw new \RuntimeException('Source ' . $source . ' is not allowed!');
        }

        $this->source = $source;
    }

    public function __toString(): string
    {
        return $this->source;
    }
}
