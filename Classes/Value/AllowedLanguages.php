<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

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
        if (!$extendAllowedLanguages instanceof AllowedLanguages) {
            throw new \RuntimeException(__CLASS__ . ' cann not be extended by ' . get_class($extendAllowedLanguages));
        }

        return new self($this->extendHelper($extendAllowedLanguages));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
