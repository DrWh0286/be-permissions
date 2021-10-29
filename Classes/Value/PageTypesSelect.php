<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class PageTypesSelect extends AbstractIntArrayField
{
    private string $fieldName = 'pagetypes_select';

    /** @param int[] $configValue */
    public static function createFromYamlConfiguration(array $configValue): PageTypesSelect
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): PageTypesSelect
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    public function extend(BeGroupFieldInterface $beGroupField): PageTypesSelect
    {
        return new self($this->extendHelper($beGroupField));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
