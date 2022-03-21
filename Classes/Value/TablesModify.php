<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class TablesModify extends AbstractStringArrayField
{
    private string $fieldName = 'tables_modify';

    /**
     * @param string $dbValue
     * @return TablesModify
     */
    public static function createFromDBValue(string $dbValue): TablesModify
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    /**
     * @param string[] $configValue
     * @return TablesModify
     */
    public static function createFromYamlConfiguration(array $configValue): TablesModify
    {
        return new self($configValue);
    }

    public function extend(BeGroupFieldInterface $tablesModify): TablesModify
    {
        if (!$tablesModify instanceof TablesModify) {
            throw new \RuntimeException(__CLASS__ . ' cann not be extended by ' . get_class($tablesModify));
        }

        return new self($this->extendHelper($tablesModify));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
