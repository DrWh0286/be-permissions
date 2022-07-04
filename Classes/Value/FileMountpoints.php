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

final class FileMountpoints extends AbstractIntArrayField
{
    private string $fieldName = 'file_mountpoints';

    /** @param int[] $configValue */
    public static function createFromYamlConfiguration(array $configValue): FileMountpoints
    {
        return new self($configValue);
    }

    public static function createFromDBValue(string $dbValue): FileMountpoints
    {
        return new self(self::createFromDBValueHelper($dbValue));
    }

    public function extend(BeGroupFieldInterface $fileMountpoints): FileMountpoints
    {
        if (!$fileMountpoints instanceof FileMountpoints) {
            throw new \RuntimeException(__CLASS__ . ' cann not be extended by ' . get_class($fileMountpoints));
        }

        return new self($this->extendHelper($fileMountpoints));
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
