<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

final class SubGroup extends AbstractStringArrayField
{
    private string $fieldName = 'subgroup';

    /**
     * @param string[] $configValue
     * @return SubGroup
     */
    public static function createFromYamlConfiguration(array $configValue): SubGroup
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): SubGroup
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    public function extend(BeGroupFieldInterface $subGroup): SubGroup
    {
        if (!$subGroup instanceof SubGroup) {
            throw new \RuntimeException(__CLASS__ . ' can not be extended by ' . get_class($subGroup));
        }

        return new self($this->extendHelper($subGroup));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
