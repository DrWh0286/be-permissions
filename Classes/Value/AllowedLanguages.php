<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class AllowedLanguages implements ArrayBasedFieldInterface
{
    /** @var int[] */
    private array $allowedLanguages;
    private string $fieldName = 'allowed_languages';

    /**
     * @param string $dbValue
     * @return AllowedLanguages
     */
    public static function createFromDBValue(string $dbValue): AllowedLanguages
    {
        $allowedLanguages = ($dbValue !== '') ? GeneralUtility::intExplode(',', $dbValue) : [];

        return new self($allowedLanguages);
    }

    /**
     * @param int[] $configValue
     * @return AllowedLanguages
     */
    public static function createFromYamlConfiguration($configValue): AllowedLanguages
    {
        return new self($configValue);
    }

    private function __construct(array $allowedLanguages)
    {
        $this->allowedLanguages = $allowedLanguages;
    }

    /**
     * @return int[]
     */
    public function yamlConfigurationValue(): array
    {
        return $this->allowedLanguages;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return implode(',', $this->allowedLanguages);
    }

    /**
     * @param BeGroupFieldInterface $extendAllowedLanguages
     * @return AllowedLanguages
     */
    public function extend(BeGroupFieldInterface $extendAllowedLanguages): AllowedLanguages
    {
        $newLanguageArray = array_unique(array_merge($this->allowedLanguages, $extendAllowedLanguages->allowedLanguages));
        asort($newLanguageArray);

        return AllowedLanguages::createFromYamlConfiguration(array_values($newLanguageArray));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}