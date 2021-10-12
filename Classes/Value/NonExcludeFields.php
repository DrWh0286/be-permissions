<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class NonExcludeFields implements BeGroupFieldInterface
{
    private array $nonExcludeFields;
    private string $fieldName = 'non_exclude_fields';

    public static function createFromDBValue(string $nonExcludeFields): NonExcludeFields
    {
        $basicArray = array_filter(GeneralUtility::trimExplode(',', $nonExcludeFields));

        $nonExcludeFieldsArray = [];

        foreach ($basicArray as $entry) {
            $entryArray = GeneralUtility::trimExplode(':', $entry);
            $nonExcludeFieldsArray[$entryArray[0]][] = $entryArray[1];
        }

        return new self($nonExcludeFieldsArray);
    }

    public static function createFromConfigurationArray(array $nonExcludeFieldsArray): NonExcludeFields
    {
        return new self($nonExcludeFieldsArray);
    }

    private function __construct(array $nonExcludeFields)
    {
        $this->nonExcludeFields = $nonExcludeFields;
    }

    public function asArray(): array
    {
        return $this->nonExcludeFields;
    }

    public function __toString(): string
    {
        $nonExcludeFieldsArray = [];

        foreach ($this->nonExcludeFields as $nonExcludeFieldsTable => $nonExcludeFields) {
            foreach ($nonExcludeFields as $nonExcludeField) {
                $nonExcludeFieldsArray[] = $nonExcludeFieldsTable . ':' . $nonExcludeField;
            }
        }

        return implode(',', $nonExcludeFieldsArray);
    }

    public function extend(BeGroupFieldInterface $extendingNonExcludeFields): NonExcludeFields
    {
        $extendedArray = array_merge_recursive($this->nonExcludeFields, $extendingNonExcludeFields->asArray());

        foreach ($extendedArray as $table => $fields) {
            $extendedArray[$table] = array_values(array_unique($fields));
        }

        return new NonExcludeFields($extendedArray);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
