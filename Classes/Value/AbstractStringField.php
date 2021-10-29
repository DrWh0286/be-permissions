<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

abstract class AbstractStringField implements StringBasedFieldInterface
{
    private string $value;

    public function __construct(string $title)
    {
        $this->value = $title;
    }

    public function yamlConfigurationValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
