<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class CategoryPerms extends AbstractIntArrayField
{
    private string $fieldName = 'category_perms';

    /** @param int[] $configValue */
    public static function createFromYamlConfiguration(array $configValue): CategoryPerms
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): CategoryPerms
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    public function extend(BeGroupFieldInterface $categoryPerms): CategoryPerms
    {
        if (!$categoryPerms instanceof CategoryPerms) {
            throw new \RuntimeException(__CLASS__ . ' cann not be extended by ' . get_class($categoryPerms));
        }

        return new self($this->extendHelper($categoryPerms));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
