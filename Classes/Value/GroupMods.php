<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class GroupMods extends AbstractStringArrayField
{
    private string $fieldName = 'groupMods';

    public static function createFromDBValue(string $dbValue): GroupMods
    {
        return parent::createFromDBValue($dbValue);
    }

    public static function createFromYamlConfiguration($configValue): GroupMods
    {
        return parent::createFromYamlConfiguration($configValue);
    }

    public function extend(BeGroupFieldInterface $tablesSelect): GroupMods
    {
        return parent::extend($tablesSelect);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
