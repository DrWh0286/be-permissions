<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class FileMountpoints extends AbstractIntArrayField
{
    private string $fieldName = 'file_mountpoints';

    /** @param int[] $configValue */
    public static function createFromYamlConfiguration(array $configValue): FileMountpoints
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): FileMountpoints
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    public function extend(BeGroupFieldInterface $beGroupField): FileMountpoints
    {
        return new self($this->extendHelper($beGroupField));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
