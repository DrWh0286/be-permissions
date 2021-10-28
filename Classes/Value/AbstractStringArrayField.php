<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractStringArrayField implements ArrayBasedFieldInterface
{
    private array $values;

    public static function createFromDBValue(string $dbValue): AbstractStringArrayField
    {
        $tablesSelect = ($dbValue !== '') ? GeneralUtility::trimExplode(',', $dbValue) : [];

        return new static($tablesSelect);
    }

    public static function createFromYamlConfiguration($configValue): AbstractStringArrayField
    {
        return new static($configValue);
    }

    public function __construct(array $tablesSelect)
    {
        $this->values = $tablesSelect;
    }

    public function yamlConfigurationValue(): array
    {
        return $this->values;
    }

    public function __toString(): string
    {
        return implode(',', $this->values);
    }

    public function extend(BeGroupFieldInterface $tablesSelect): AbstractStringArrayField
    {
        $tablesSelectArray = array_unique(array_merge($this->values, $tablesSelect->yamlConfigurationValue()));
        asort($tablesSelectArray);

        return new static(array_values($tablesSelectArray));
    }
}
