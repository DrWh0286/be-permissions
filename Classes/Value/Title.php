<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class Title implements StringBasedFieldInterface
{
    private string $title;
    private string $fieldName = 'title';

    public static function createFromDBValue(string $dbValue): Title
    {
        return new self($dbValue);
    }

    public static function createFromYamlConfiguration($configValue): Title
    {
        return new self($configValue);
    }

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function yamlConfigurationValue(): string
    {
        return $this->title;
    }

    public function extend(BeGroupFieldInterface $beGroupField): Title
    {
        return clone $this;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function __toString(): string
    {
        return $this->title;
    }
}
