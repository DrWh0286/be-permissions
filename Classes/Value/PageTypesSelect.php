<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class PageTypesSelect extends AbstractIntArrayField
{
    private string $fieldName = 'pagetypes_select';

    public static function createFromYamlConfiguration($configValue): PageTypesSelect
    {
        return parent::createFromYamlConfiguration($configValue);
    }

    public static function createFromDBValue(string $dbValue): PageTypesSelect
    {
        return parent::createFromDBValue($dbValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): PageTypesSelect
    {
        return parent::extend($beGroupField);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
