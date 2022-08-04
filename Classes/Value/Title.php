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

final class Title extends AbstractStringField
{
    private string $fieldName = 'title';

    public static function createFromDBValue(string $dbValue): Title
    {
        return new self($dbValue);
    }

    public static function createFromYamlConfiguration(string $configValue): Title
    {
        return new self($configValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): Title
    {
        return clone $this;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}
