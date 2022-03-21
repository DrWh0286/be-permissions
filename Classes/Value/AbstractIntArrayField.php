<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractIntArrayField implements ArrayBasedFieldInterface
{
    /** @var array<int> */
    private array $fieldValues;

    /** @return array<int> */
    public static function createFromDBValueHelper(string $dbValue): array
    {
        return ($dbValue === '') ? [] : GeneralUtility::intExplode(',', $dbValue);
    }

    /**
     * @param array<int> $fieldValues
     */
    public function __construct(array $fieldValues)
    {
        asort($fieldValues);
        $this->fieldValues = array_values($fieldValues);
    }

    /**
     * @return array<int>
     */
    public function yamlConfigurationValue(): array
    {
        return $this->fieldValues;
    }

    public function __toString(): string
    {
        return implode(',', $this->fieldValues);
    }

    /** @return array<int> */
    public function extendHelper(AbstractIntArrayField $abstractIntArrayField): array
    {
        $array = array_unique(array_merge($this->fieldValues, $abstractIntArrayField->yamlConfigurationValue()));
        asort($array);

        return array_values($array);
    }
}
