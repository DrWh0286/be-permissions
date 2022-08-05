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

namespace SebastianHofer\BePermissions\Repository;

use SebastianHofer\BePermissions\Collection\BeGroupConfigurationCollection;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Configuration\ConfigurationFileMissingException;
use SebastianHofer\BePermissions\Value\Identifier;

interface BeGroupConfigurationRepositoryInterface
{
    public function write(BeGroupConfiguration $beGroupConfiguration): void;

    /**
     * @throws ConfigurationFileMissingException
     */
    public function load(Identifier $identifier, string $configPath): BeGroupConfiguration;

    /**
     * @param string $configPath
     * @return BeGroupConfigurationCollection
     */
    public function loadAll(string $configPath): BeGroupConfigurationCollection;

    /**
     * @throws ConfigurationFileMissingException
     */
    public function loadYamlString(Identifier $identifier, string $configPath): string;
}
