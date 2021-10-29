<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class Title extends AbstractStringField
{
    private string $fieldName = 'title';

    public static function createFromDBValue(string $dbValue): Title
    {
        return new self($dbValue);
    }

    public static function createFromYamlConfiguration(string $configValue): Title
    {
        return new self($configValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): Title
    {
        return clone $this;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
