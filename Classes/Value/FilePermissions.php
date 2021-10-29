<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class FilePermissions extends AbstractStringArrayField
{
    private string $fieldName = 'file_permissions';

    public static function createFromDBValue(string $dbValue): FilePermissions
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    /**
     * @param string[] $configValue
     * @return FilePermissions
     */
    public static function createFromYamlConfiguration(array $configValue): FilePermissions
    {
        return new self($configValue);
    }

    public function extend(BeGroupFieldInterface $filePermissions): FilePermissions
    {
        return new self($this->extendHelper($filePermissions));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
