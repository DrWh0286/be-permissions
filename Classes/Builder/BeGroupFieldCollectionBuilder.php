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

namespace SebastianHofer\BePermissions\Builder;

use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Collection\DuplicateBeGroupFieldException;
use SebastianHofer\BePermissions\Value\BeGroupFieldFactoryInterface;
use SebastianHofer\BePermissions\Value\BeGroupFieldInterface;

final class BeGroupFieldCollectionBuilder implements BeGroupFieldCollectionBuilderInterface
{
    private BeGroupFieldFactoryInterface $beGroupFieldFactory;

    /**
     * BeGroupFieldCollectionBuilder constructor.
     * @param BeGroupFieldFactoryInterface $beGroupFieldFactory
     */
    public function __construct(BeGroupFieldFactoryInterface $beGroupFieldFactory)
    {
        $this->beGroupFieldFactory = $beGroupFieldFactory;
    }

    /**
     * @param array<string> $dbValues
     * @return BeGroupFieldCollection
     * @throws DuplicateBeGroupFieldException
     */
    public function buildFromDatabaseValues(array $dbValues): BeGroupFieldCollection
    {
        $collection = new BeGroupFieldCollection();

        foreach ($dbValues as $dbFieldName => $dbValue) {
            $valueObject = $this->beGroupFieldFactory->buildFromFieldNameAndDatabaseValue($dbFieldName, (string)$dbValue);
            if ($valueObject instanceof BeGroupFieldInterface) {
                $collection->add($valueObject);
            }
        }

        return $collection;
    }

    /**
     * @param array<string, array<int|string, array<int, string>|string>> $configurationArray
     * @return BeGroupFieldCollection
     * @throws DuplicateBeGroupFieldException
     */
    public function buildFromConfigurationArray(array $configurationArray): BeGroupFieldCollection
    {
        $collection = new BeGroupFieldCollection();

        foreach ($configurationArray as $dbFieldName => $dbValue) {
            $valueObject = $this->beGroupFieldFactory->buildFromFieldNameAndYamlValue($dbFieldName, $dbValue);
            if ($valueObject instanceof BeGroupFieldInterface) {
                $collection->add($valueObject);
            }
        }

        return $collection;
    }
}
