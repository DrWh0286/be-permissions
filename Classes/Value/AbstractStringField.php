<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

abstract class AbstractStringField implements StringBasedFieldInterface
{
    private string $value;

    public static function createFromDBValue(string $dbValue): AbstractStringField
    {
        return new static($dbValue);
    }

    public static function createFromYamlConfiguration($configValue): AbstractStringField
    {
        return new static($configValue);
    }

    public function __construct(string $title)
    {
        $this->value = $title;
    }

    public function yamlConfigurationValue(): string
    {
        return $this->value;
    }

    public function extend(BeGroupFieldInterface $beGroupField): AbstractStringField
    {
        return clone $this;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
