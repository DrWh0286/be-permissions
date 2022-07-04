<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "form_consent".
 *
 * Copyright (C) 2022 Sebastian Hofer <sebastian.hofer@s-hofer.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace SebastianHofer\BePermissions\Value;

use SebastianHofer\BePermissions\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class NonExcludeFields implements ArrayBasedFieldInterface
{
    /** @var array<string, array<string>> */
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

    /** @param array<string, array<string>> $configValue */
    public static function createFromYamlConfiguration(array $configValue): NonExcludeFields
    {
        return new self($configValue);
    }

    /** @param array<string, array<string>> $nonExcludeFields */
    private function __construct(array $nonExcludeFields)
    {
        ArrayUtility::ksortNestedAsort($nonExcludeFields);

        foreach ($nonExcludeFields as $excludeFields) {
            asort($excludeFields);
        }

        $this->nonExcludeFields = $nonExcludeFields;
    }

    /** @return array<string, array<string>> */
    public function yamlConfigurationValue(): array
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
        $extendedArray = array_merge_recursive($this->nonExcludeFields, (array)$extendingNonExcludeFields->yamlConfigurationValue());

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
