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

use SebastianHofer\BePermissions\Configuration\ExtensionConfigurationInterface;
use SebastianHofer\BePermissions\Configuration\NoValueObjectConfiguredException;

final class BeGroupFieldFactory implements BeGroupFieldFactoryInterface
{
    private ExtensionConfigurationInterface $extensionConfiguration;

    public function __construct(ExtensionConfigurationInterface $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    public function buildFromFieldNameAndYamlValue(string $fieldName, $value): ?BeGroupFieldInterface
    {
        try {
            $valueClass = $this->extensionConfiguration->getClassNameByFieldName($fieldName);
        } catch (NoValueObjectConfiguredException $exception) {
            // @todo: Log info here.
            return null;
        }

        $implementArray = class_implements($valueClass) ?: [];

        if (in_array(ArrayBasedFieldInterface::class, $implementArray) && !is_array($value)) {
            throw new \InvalidArgumentException('Value for field ' . $fieldName . ' must be of type array!');
        }

        if (in_array(StringBasedFieldInterface::class, $implementArray) && !is_string($value)) {
            throw new \InvalidArgumentException('Value for field ' . $fieldName . ' must be of type string!');
        }

        return $valueClass::createFromYamlConfiguration($value);
    }

    public function buildFromFieldNameAndDatabaseValue(string $fieldName, string $value): ?BeGroupFieldInterface
    {
        try {
            $valueClass = $this->extensionConfiguration->getClassNameByFieldName($fieldName);
        } catch (NoValueObjectConfiguredException $exception) {
            // @todo: Log info here.
            return null;
        }

        return $valueClass::createFromDBValue($value);
    }
}
