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

use InvalidArgumentException;

final class DeployProcessing extends AbstractStringField
{
    private const EXTEND = 'extend';
    private const OVERRULE = 'overrule';
    private const DEFAULT_VALUE = self::EXTEND;
    private const ALLOWED_VALUES = [
        self::EXTEND,
        self::OVERRULE
    ];

    private string $fieldName = 'deploy_processing';

    public static function createFromDBValue(string $dbValue): DeployProcessing
    {
        if (empty($dbValue)) {
            $dbValue = self::DEFAULT_VALUE;
        }

        return new self($dbValue);
    }

    public static function createFromYamlConfiguration(string $configValue): DeployProcessing
    {
        return new self($configValue);
    }

    public static function createWithDefault(): DeployProcessing
    {
        return new self(self::DEFAULT_VALUE);
    }

    public function __construct(string $value)
    {
        self::validateCreateValue($value);

        parent::__construct($value);
    }

    /**
     * @param string $languageFilePrefix
     * @return string[][]
     */
    public static function tcaItems(string $languageFilePrefix = ''): array
    {
        $tcaItems = [];

        foreach (self::ALLOWED_VALUES as $allowedValue) {
            $tcaItems[] = [$languageFilePrefix . $allowedValue, $allowedValue];
        }

        return $tcaItems;
    }

    public function extend(BeGroupFieldInterface $beGroupField): DeployProcessing
    {
        if (!$beGroupField instanceof DeployProcessing) {
            throw new \RuntimeException('Wrong be_groups field is given. ' . get_class($beGroupField) . ' given instead of expected ' . get_class($this));
        }

        return clone $beGroupField;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function isExtend(): bool
    {
        return (string)$this === self::EXTEND;
    }

    public function isOverrule(): bool
    {
        return (string)$this === self::OVERRULE;
    }

    private static function validateCreateValue(string $createValue): void
    {
        if (!in_array($createValue, self::ALLOWED_VALUES)) {
            throw new InvalidArgumentException('Value ' . $createValue . ' not allowed in ' . __CLASS__);
        }
    }
}
