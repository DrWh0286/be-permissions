<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

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

    public function extend(BeGroupFieldInterface $fileMountpoints): FileMountpoints
    {
        if (!$fileMountpoints instanceof FileMountpoints) {
            throw new \RuntimeException(__CLASS__ . ' cann not be extended by ' . get_class($fileMountpoints));
        }

        return new self($this->extendHelper($fileMountpoints));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
