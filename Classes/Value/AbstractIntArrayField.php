<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractIntArrayField implements ArrayBasedFieldInterface
{
    private array $fieldValues;

    public static function createFromDBValue(string $dbValue): AbstractIntArrayField
    {
        $dbMountpoints = ($dbValue === '') ? [] : GeneralUtility::intExplode(',', $dbValue);

        return new static($dbMountpoints);
    }

    public function __construct(array $fieldValues)
    {
        asort($fieldValues);
        $this->fieldValues = array_values(array_filter($fieldValues));
    }

    public static function createFromYamlConfiguration($configValue): AbstractIntArrayField
    {
        return new static($configValue);
    }

    public function yamlConfigurationValue(): array
    {
        return $this->fieldValues;
    }

    public function __toString(): string
    {
        return implode(',', $this->fieldValues);
    }

    public function extend(BeGroupFieldInterface $extendDbMountpoints): AbstractIntArrayField
    {
        $array = array_unique(array_merge($this->fieldValues, $extendDbMountpoints->yamlConfigurationValue()));
        asort($array);

        return new static(array_values($array));
    }
}
