<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class CategoryPerms extends AbstractIntArrayField
{
    private string $fieldName = 'category_perms';

    public static function createFromYamlConfiguration($configValue): CategoryPerms
    {
        return parent::createFromYamlConfiguration($configValue);
    }

    public static function createFromDBValue(string $dbValue): CategoryPerms
    {
        return parent::createFromDBValue($dbValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): CategoryPerms
    {
        return parent::extend($beGroupField);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
