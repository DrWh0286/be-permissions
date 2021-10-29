<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

final class AllowedLanguages extends AbstractIntArrayField
{
    private string $fieldName = 'allowed_languages';

    /**
     * @param string $dbValue
     * @return AllowedLanguages
     */
    public static function createFromDBValue(string $dbValue): AllowedLanguages
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    /**
     * @param int[] $configValue
     * @return AllowedLanguages
     */
    public static function createFromYamlConfiguration(array $configValue): AllowedLanguages
    {
        return new self($configValue);
    }

    /**
     * @param BeGroupFieldInterface $extendAllowedLanguages
     * @return AllowedLanguages
     */
    public function extend(BeGroupFieldInterface $extendAllowedLanguages): AllowedLanguages
    {
        return new self($this->extendHelper($extendAllowedLanguages));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
