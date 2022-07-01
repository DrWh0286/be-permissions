<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

abstract class AbstractBooleanField implements BooleanBasedFieldInterface
{
    private bool $boolValue;

    public function __construct(bool $boolValue)
    {
        $this->boolValue = $boolValue;
    }

    public function yamlConfigurationValue(): bool
    {
        return $this->boolValue;
    }

    public function __toString(): string
    {
        return $this->boolValue ? '1' : '0';
    }
}
