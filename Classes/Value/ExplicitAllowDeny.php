<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class ExplicitAllowDeny implements ArrayBasedFieldInterface
{
    /** @var array<string, array<string, array<string, string>>> */
    private array $explicitAllowDeny;
    private string $fieldName = 'explicit_allowdeny';

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

    /** @param array<string, array<string, array<string, string>>> $configValue */
    public static function createFromYamlConfiguration(array $configValue): ExplicitAllowDeny
    {
        return new self($configValue);
    }

    /** @param array<string, array<string, array<string, string>>> $explicitAllowDeny */
    private function __construct(array $explicitAllowDeny)
    {
        $this->explicitAllowDeny = $explicitAllowDeny;
    }

    /** @return array<string, array<string, array<string, string>>> */
    public function yamlConfigurationValue(): array
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

    public function extend(BeGroupFieldInterface $extendingExplicitAllowDeny): ExplicitAllowDeny
    {
        $extendedArray = array_replace_recursive($this->explicitAllowDeny, (array)$extendingExplicitAllowDeny->yamlConfigurationValue());

        return new ExplicitAllowDeny($extendedArray);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
