<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class MfaProviders extends AbstractStringArrayField
{
    private string $fieldName = 'mfa_providers';

    public static function createFromYamlConfiguration(array $configValue): MfaProviders
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): MfaProviders
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    public function extend(BeGroupFieldInterface $availableWidgets): MfaProviders
    {
        return new self($this->extendHelper($availableWidgets));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
