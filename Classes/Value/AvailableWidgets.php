<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class AvailableWidgets extends AbstractStringArrayField
{
    private string $fieldName = 'availableWidgets';

    /**
     * @inheritDoc
     */
    public static function createFromYamlConfiguration(array $configValue): AvailableWidgets
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): AvailableWidgets
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    public function extend(BeGroupFieldInterface $availableWidgets): BeGroupFieldInterface
    {
        return new self($this->extendHelper($availableWidgets));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
