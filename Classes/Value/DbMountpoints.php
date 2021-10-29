<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class DbMountpoints extends AbstractIntArrayField
{
    private string $fieldName = 'db_mountpoints';

    public static function createFromDBValue(string $dbValue): DbMountpoints
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    /** @param int[] $configValue */
    public static function createFromYamlConfiguration(array $configValue): DbMountpoints
    {
        return new self($configValue);
    }

    public function extend(BeGroupFieldInterface $extendDbMountpoints): DbMountpoints
    {
        return new self($this->extendHelper($extendDbMountpoints));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
