<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class TablesModify extends AbstractStringArrayField
{
    private string $fieldName = 'tables_modify';

    public static function createFromDBValue(string $dbValue): TablesModify
    {
        return parent::createFromDBValue($dbValue);
    }

    public static function createFromYamlConfiguration($configValue): TablesModify
    {
        return parent::createFromYamlConfiguration($configValue);
    }

    public function extend(BeGroupFieldInterface $tablesSelect): TablesModify
    {
        return parent::extend($tablesSelect);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
