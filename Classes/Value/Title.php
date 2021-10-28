<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class Title extends AbstractStringField
{
    private string $fieldName = 'title';

    public static function createFromDBValue(string $dbValue): Title
    {
        return parent::createFromDBValue($dbValue);
    }

    public static function createFromYamlConfiguration($configValue): Title
    {
        return parent::createFromYamlConfiguration($configValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): AbstractStringField
    {
        return clone $this;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
