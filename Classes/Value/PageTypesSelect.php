<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class PageTypesSelect implements ArrayBasedFieldInterface
{
    /** @var int[] */
    private array $pageTypes;
    private string $fieldName = 'pagetypes_select';

    public static function createFromYamlConfiguration($configValue): ArrayBasedFieldInterface
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): BeGroupFieldInterface
    {
        $pageTypes = GeneralUtility::intExplode(',', $dbValue);

        return new self($pageTypes);
    }

    public function __construct(array $pageTypes)
    {
        asort($pageTypes);
        $this->pageTypes = array_values(array_filter($pageTypes));
    }

    public function yamlConfigurationValue(): array
    {
        return $this->pageTypes;
    }

    public function extend(BeGroupFieldInterface $beGroupField): PageTypesSelect
    {
        $newPageTypesSelectedArray = array_unique(array_merge($this->pageTypes, $beGroupField->yamlConfigurationValue()));
        asort($newPageTypesSelectedArray);

        return new PageTypesSelect(array_values($newPageTypesSelectedArray));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function __toString(): string
    {
        return implode(',', $this->pageTypes);
    }
}
