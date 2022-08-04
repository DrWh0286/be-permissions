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

final class CodeManagedGroup extends AbstractBooleanField
{
    private string $fieldName = 'code_managed_group';

    public static function createFromDBValue(string $dbValue): CodeManagedGroup
    {
        $createValue = (bool)((int)$dbValue);

        return new self($createValue);
    }

    public function extend(BeGroupFieldInterface $beGroupField): CodeManagedGroup
    {
        if (!$beGroupField instanceof CodeManagedGroup) {
            throw new \RuntimeException('Wrong be_groups field is given. ' . get_class($beGroupField) . ' given instead of expected ' . get_class($this));
        }

        return clone $beGroupField;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public static function createFromYamlConfiguration(bool $configValue): CodeManagedGroup
    {
        return new self($configValue);
    }
}
