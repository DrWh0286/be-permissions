<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class TablesSelect extends AbstractStringArrayField
{
    private string $fieldName = 'tables_select';

    public static function createFromDBValue(string $dbValue): TablesSelect
    {
        return parent::createFromDBValue($dbValue);
    }

    public static function createFromYamlConfiguration($configValue): TablesSelect
    {
        return parent::createFromYamlConfiguration($configValue);
    }

    public function extend(BeGroupFieldInterface $tablesSelect): TablesSelect
    {
        return parent::extend($tablesSelect);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
