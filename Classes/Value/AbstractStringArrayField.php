<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "be_permissions".
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractStringArrayField implements ArrayBasedFieldInterface
{
    /** @var array<string> */
    private array $values;

    /** @return array<string> */
    protected static function createFromDBValueHelper(string $dbValue): array
    {
        return ($dbValue !== '') ? GeneralUtility::trimExplode(',', $dbValue) : [];
    }

    /**
     * @param array<string> $values
     */
    public function __construct(array $values)
    {
        asort($values);
        $this->values = array_values($values);
    }

    /**
     * @return array<string>
     */
    public function yamlConfigurationValue(): array
    {
        return $this->values;
    }

    public function __toString(): string
    {
        return implode(',', $this->values);
    }

    /** @return array<string> */
    protected function extendHelper(AbstractStringArrayField $tablesSelect): array
    {
        $tablesSelectArray = array_unique(array_merge($this->values, $tablesSelect->yamlConfigurationValue()));
        asort($tablesSelectArray);

        return array_values($tablesSelectArray);
    }
}
