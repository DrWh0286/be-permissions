<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class FilePermissions extends AbstractStringArrayField
{
    private string $fieldName = 'file_permissions';

    public static function createFromDBValue(string $dbValue): FilePermissions
    {
        return parent::createFromDBValue($dbValue);
    }

    public static function createFromYamlConfiguration($configValue): FilePermissions
    {
        return parent::createFromYamlConfiguration($configValue);
    }

    public function extend(BeGroupFieldInterface $tablesSelect): FilePermissions
    {
        return parent::extend($tablesSelect);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
