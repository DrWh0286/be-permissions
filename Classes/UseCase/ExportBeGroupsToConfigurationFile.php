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

namespace SebastianHofer\BePermissions\UseCase;

use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepositoryInterface;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Core\Environment;

final class ExportBeGroupsToConfigurationFile
{
    private BeGroupRepositoryInterface $beGroupRepository;
    private BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository;

    public function __construct(BeGroupRepositoryInterface $beGroupRepository, BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository)
    {
        $this->beGroupRepository = $beGroupRepository;
        $this->beGroupConfigurationRepository = $beGroupConfigurationRepository;
    }

    public function exportGroup(Identifier $identifier): void
    {
        $group = $this->beGroupRepository->findOneByIdentifier($identifier);

        if ($group instanceof BeGroup) {
            $configPath = Environment::getConfigPath();
            $configuration = BeGroupConfiguration::createFromBeGroup($group, $configPath);
            $this->beGroupConfigurationRepository->write($configuration);
        }
    }

    public function exportGroups(): void
    {
        $beGroups = $this->beGroupRepository->findAllCodeManaged();
        $configPath = Environment::getConfigPath();

        /** @var BeGroup $beGroup */
        foreach ($beGroups as $beGroup) {
            $configuration = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);
            $this->beGroupConfigurationRepository->write($configuration);
        }
    }
}
