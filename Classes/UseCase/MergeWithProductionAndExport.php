<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\UseCase;

use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepositoryInterface;
use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;
use Pluswerk\BePermissions\Value\Identifier;
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
    public function mergeAndExportGroups(): void
    {
        // Export local groups
        $this->exportBeGroupToConfigurationFile->exportGroups();

        // synchronize production groups
        $this->synchronizeBeGroupsFromProduction->syncBeGroups();

        $configPath = Environment::getConfigPath();
        $configurations = $this->beGroupConfigurationRepository->loadAll($configPath);
        foreach ($configurations as $configuration) {
            $beGroup = $this->beGroupRepository->findOneByIdentifier($configuration->identifier());

            if ($beGroup instanceof BeGroup) {
                $updatedBeGroup = $beGroup->extendByConfiguration($configuration);

                $this->beGroupRepository->update($updatedBeGroup);
            } else {
                $beGroup = new BeGroup($configuration->identifier(), $configuration->beGroupFieldCollection());

                $this->beGroupRepository->add($beGroup);
            }
        }

        // Export local groups again
        $this->exportBeGroupToConfigurationFile->exportGroups();
    }

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
