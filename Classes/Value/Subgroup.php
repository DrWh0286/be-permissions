<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class Subgroup extends AbstractIntArrayField
{
    private string $fieldName = 'subgroup';

    public static function createFromYamlConfiguration($configValue): Subgroup
    {
        return parent::createFromYamlConfiguration($configValue);
    }

    public static function createFromDBValue(string $dbValue): Subgroup
    {
        return parent::createFromDBValue($dbValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): Subgroup
    {
        return parent::extend($beGroupField);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
