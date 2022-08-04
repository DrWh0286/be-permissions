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
use TYPO3\CMS\Core\Core\Environment;

final class DeployBeGroups
{
    private BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository;
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository, BeGroupRepositoryInterface $beGroupRepository)
    {
        $this->beGroupConfigurationRepository = $beGroupConfigurationRepository;
        $this->beGroupRepository = $beGroupRepository;
    }

    public function deployGroups(): void
    {
        $configPath = Environment::getConfigPath();
        $configurations = $this->beGroupConfigurationRepository->loadAll($configPath);

        /** @var BeGroupConfiguration $configuration */
        foreach ($configurations as $configuration) {
            $beGroup = $this->beGroupRepository->findOneByIdentifier($configuration->identifier());

            // @todo: find a nicer solution than nested if
            if ($configuration->getDeploymentProcessing()->isOverrule()) {
                if ($beGroup instanceof BeGroup) {
                    $updatedBeGroup = $beGroup->overruleByConfiguration($configuration);

                    $this->beGroupRepository->update($updatedBeGroup);
                } else {
                    $beGroup = new BeGroup($configuration->identifier(), $configuration->beGroupFieldCollection());

                    $this->beGroupRepository->add($beGroup);
                }
            } else {
                if ($beGroup instanceof BeGroup) {
                    $updatedBeGroup = $beGroup->extendByConfiguration($configuration);

                    $this->beGroupRepository->update($updatedBeGroup);
                } else {
                    $beGroup = new BeGroup($configuration->identifier(), $configuration->beGroupFieldCollection());

                    $this->beGroupRepository->add($beGroup);
                }
            }
        }
    }
}
