<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class TablesSelect implements ArrayBasedFieldInterface
{
    private array $tablesSelect;
    private string $fieldName = 'tables_select';

    public static function createFromDBValue(string $dbValue): TablesSelect
    {
        $tablesSelect = ($dbValue !== '') ? GeneralUtility::trimExplode(',', $dbValue) : [];

        return new self($tablesSelect);
    }

    public static function createFromYamlConfiguration($configValue): TablesSelect
    {
        return new self($configValue);
    }

    public function __construct(array $tablesSelect)
    {
        $this->tablesSelect = $tablesSelect;
    }

    public function yamlConfigurationValue(): array
    {
        return $this->tablesSelect;
    }

    public function __toString(): string
    {
        return implode(',', $this->tablesSelect);
    }

    public function extend(BeGroupFieldInterface $tablesSelect): TablesSelect
    {
        $tablesSelectArray = array_unique(array_merge($this->tablesSelect, $tablesSelect->yamlConfigurationValue()));
        asort($tablesSelectArray);

        return new self(array_values($tablesSelectArray));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
