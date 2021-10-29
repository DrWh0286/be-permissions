<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class Subgroup extends AbstractIntArrayField
{
    private string $fieldName = 'subgroup';

    /** @param int[] $configValue */
    public static function createFromYamlConfiguration(array $configValue): Subgroup
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): Subgroup
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    public function extend(BeGroupFieldInterface $beGroupField): Subgroup
    {
        return new self($this->extendHelper($beGroupField));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
