<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ExplicitAllowDeny
{

    private array $explicitAllowDeny;

    public static function createFromDBValue(string $dbValue): ExplicitAllowDeny
    {
        $basicArray = array_filter(GeneralUtility::trimExplode(',', $dbValue));

        $explicitAllowDeny = [];

        foreach ($basicArray as $entry) {
            $entryArray = GeneralUtility::trimExplode(':', $entry);
            $explicitAllowDeny[$entryArray[0]][$entryArray[1]][$entryArray[2]] = $entryArray[3];
        }

        return new self($explicitAllowDeny);
    }

    public static function createFromConfigurationArray(array $configArray): ExplicitAllowDeny
    {
        return new self($configArray);
    }

    private function __construct(array $explicitAllowDeny)
    {
        $this->explicitAllowDeny = $explicitAllowDeny;
    }

    public function asArray(): array
    {
        return $this->explicitAllowDeny;
    }

    public function __toString(): string
    {
        $explicitAllowDenyStringsArray = [];

        foreach ($this->explicitAllowDeny as $table => $fields) {
            $tableString = $table . ':';
            foreach ($fields as $field => $values) {
                $fieldString = $field . ':';
                foreach ($values as $value => $permission) {
                    $valuePermissionString = $value . ':' . $permission;
                    $explicitAllowDenyStringsArray[] = $tableString . $fieldString . $valuePermissionString;
                }
            }
        }

        return implode(',', $explicitAllowDenyStringsArray);
    }

    public function extend(ExplicitAllowDeny $extendingExplicitAllowDeny): ExplicitAllowDeny
    {
        $extendedArray = array_replace_recursive($this->explicitAllowDeny, $extendingExplicitAllowDeny->asArray());

        return new ExplicitAllowDeny($extendedArray);
    }
}
