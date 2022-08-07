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

use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Configuration\ConfigurationFileMissingException;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepositoryInterface;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Repository\GroupNotFullyImportedException;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Core\Environment;

final class MergeWithProductionAndExport
{
    private SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction;
    private BeGroupRepositoryInterface $beGroupRepository;
    private BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository;
    private ExportBeGroupsToConfigurationFile $exportBeGroupToConfigurationFile;

    public function __construct(
        SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction,
        BeGroupRepositoryInterface $beGroupRepository,
        BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository,
        ExportBeGroupsToConfigurationFile $exportBeGroupToConfigurationFile
    ) {
        $this->synchronizeBeGroupsFromProduction = $synchronizeBeGroupsFromProduction;
        $this->beGroupRepository = $beGroupRepository;
        $this->beGroupConfigurationRepository = $beGroupConfigurationRepository;
        $this->exportBeGroupToConfigurationFile = $exportBeGroupToConfigurationFile;
    }

    // Not a good way - in case of overrule a synch would be useless. We need a more detailed strategy here...
    // Maybe ignore overrule here and use only extend mode?
    /**
     * @throws GroupNotFullyImportedException
     */
    public function mergeAndExportGroups(): void
    {
        // Export local groups
        $this->exportBeGroupToConfigurationFile->exportGroups();

        // synchronize remote groups
        $this->synchronizeBeGroupsFromProduction->syncBeGroups();

        $configPath = Environment::getConfigPath();
        $configurations = $this->beGroupConfigurationRepository->loadAll($configPath);

        $groupsToDeploy = new BeGroupCollection();
        /** @var BeGroupConfiguration $configuration */
        foreach ($configurations as $configuration) {
            $beGroup = $this->beGroupRepository->findOneByIdentifier($configuration->identifier());

            if ($beGroup instanceof BeGroup) {
                $beGroupToDeploy = $beGroup->extendByConfiguration($configuration);
            } else {
                $beGroupToDeploy = new BeGroup($configuration->identifier(), $configuration->beGroupFieldCollection());
            }

            $groupsToDeploy->add($beGroupToDeploy);
        }

        $this->beGroupRepository->addOrUpdateBeGroups($groupsToDeploy);

        // Export local groups again
        $this->exportBeGroupToConfigurationFile->exportGroups();
    }

    /**
     * @throws GroupNotFullyImportedException|ConfigurationFileMissingException
     */
    public function mergeAndExportGroup(Identifier $identifier): void
    {
        $this->exportBeGroupToConfigurationFile->exportGroup($identifier);

        $this->synchronizeBeGroupsFromProduction->syncBeGroup($identifier);

        $configPath = Environment::getConfigPath();
        $configuration = $this->beGroupConfigurationRepository->load($identifier, $configPath);
        $beGroup = $this->beGroupRepository->findOneByIdentifier($configuration->identifier());

        if ($beGroup instanceof BeGroup) {
            $updatedBeGroup = $beGroup->extendByConfiguration($configuration);

            $this->beGroupRepository->update($updatedBeGroup);
        } else {
            $beGroup = new BeGroup($configuration->identifier(), $configuration->beGroupFieldCollection());

            $this->beGroupRepository->add($beGroup);
        }

        $this->exportBeGroupToConfigurationFile->exportGroup($identifier);
    }
}
