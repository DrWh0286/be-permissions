<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class TablesSelect extends AbstractStringArrayField
{
    private string $fieldName = 'tables_select';

    public static function createFromDBValue(string $dbValue): TablesSelect
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    /**
     * @param string[] $configValue
     * @return TablesSelect
     */
    public static function createFromYamlConfiguration(array $configValue): TablesSelect
    {
        return new self($configValue);
    }

    public function extend(BeGroupFieldInterface $tablesSelect): TablesSelect
    {
        return new self($this->extendHelper($tablesSelect));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
