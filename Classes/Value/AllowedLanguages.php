<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class AllowedLanguages
{
    /** @var int[] */
    private array $allowedLanguages;

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
     * @param int[] $confArray
     * @return AllowedLanguages
     */
    public static function createFromConfigurationArray(array $confArray): AllowedLanguages
    {
        return new self($confArray);
    }

    private function __construct(array $allowedLanguages)
    {
        $this->allowedLanguages = $allowedLanguages;
    }

    /**
     * @return int[]
     */
    public function asArray(): array
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
     * @param AllowedLanguages $extendAllowedLanguages
     * @return AllowedLanguages
     */
    public function extend(AllowedLanguages $extendAllowedLanguages): AllowedLanguages
    {
        $newLanguageArray = array_unique(array_merge($this->allowedLanguages, $extendAllowedLanguages->allowedLanguages));
        asort($newLanguageArray);

        return AllowedLanguages::createFromConfigurationArray(array_values($newLanguageArray));
    }
}
