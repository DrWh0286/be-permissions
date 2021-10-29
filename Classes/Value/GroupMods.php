<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class GroupMods extends AbstractStringArrayField
{
    private string $fieldName = 'groupMods';

    public static function createFromDBValue(string $dbValue): GroupMods
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    /**
     * @param string[] $configValue
     * @return GroupMods
     */
    public static function createFromYamlConfiguration(array $configValue): GroupMods
    {
        return new self($configValue);
    }

    public function extend(BeGroupFieldInterface $groupMods): GroupMods
    {
        return new self($this->extendHelper($groupMods));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
