<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class LockToDomain extends AbstractStringField
{
    private string $fieldName = 'lockToDomain';

    public static function createFromDBValue(string $dbValue): LockToDomain
    {
        return new self($dbValue);
    }

    public static function createFromYamlConfiguration(string $configValue): LockToDomain
    {
        return new self($configValue);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function extend(BeGroupFieldInterface $beGroupField): LockToDomain
    {
        return new self((string)$beGroupField);
    }
}
