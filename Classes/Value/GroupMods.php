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

final class GroupMods extends AbstractStringArrayField
{
    private string $fieldName = 'groupMods';

    public static function createFromDBValue(string $dbValue): GroupMods
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    /**
     * @param string[] $configValue
     * @return GroupMods
     */
    public static function createFromYamlConfiguration(array $configValue): GroupMods
    {
        return new self($configValue);
    }

    public function extend(BeGroupFieldInterface $groupMods): GroupMods
    {
        if (!$groupMods instanceof GroupMods) {
            throw new \RuntimeException(__CLASS__ . ' cann not be extended by ' . get_class($groupMods));
        }

        return new self($this->extendHelper($groupMods));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
