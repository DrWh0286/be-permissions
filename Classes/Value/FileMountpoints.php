<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class FileMountpoints extends AbstractIntArrayField
{
    private string $fieldName = 'file_mountpoints';

    public static function createFromYamlConfiguration($configValue): FileMountpoints
    {
        return parent::createFromYamlConfiguration($configValue);
    }

    public static function createFromDBValue(string $dbValue): FileMountpoints
    {
        return parent::createFromDBValue($dbValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): FileMountpoints
    {
        return parent::extend($beGroupField);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
