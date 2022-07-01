<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

final class MfaProviders extends AbstractStringArrayField
{
    private string $fieldName = 'mfa_providers';

    /**
     * @param array<string> $configValue
     * @return MfaProviders
     */
    public static function createFromYamlConfiguration(array $configValue): MfaProviders
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): MfaProviders
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    public function extend(BeGroupFieldInterface $mfaProviders): MfaProviders
    {
        if (!$mfaProviders instanceof MfaProviders) {
            throw new \RuntimeException(__CLASS__ . ' cann not be extended by ' . get_class($mfaProviders));
        }

        return new self($this->extendHelper($mfaProviders));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
