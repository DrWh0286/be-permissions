<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\UseCase;

use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepositoryInterface;
use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\Source;
use TYPO3\CMS\Core\Core\Environment;

final class MergeWithProductionAndExport
{
    private BulkExportBeGroupsToConfigurationFiles $bulkExportBeGroupsToConfigurationFiles;
    private SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction;
    private BeGroupRepositoryInterface $beGroupRepository;
    private BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository;
    private ExportBeGroupToConfigurationFile $exportBeGroupToConfigurationFile;

    public function __construct(
        BulkExportBeGroupsToConfigurationFiles $bulkExportBeGroupsToConfigurationFiles,
        SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction,
        BeGroupRepositoryInterface $beGroupRepository,
        BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository,
        ExportBeGroupToConfigurationFile $exportBeGroupToConfigurationFile
    ) {
        $this->bulkExportBeGroupsToConfigurationFiles = $bulkExportBeGroupsToConfigurationFiles;
        $this->synchronizeBeGroupsFromProduction = $synchronizeBeGroupsFromProduction;
        $this->beGroupRepository = $beGroupRepository;
        $this->beGroupConfigurationRepository = $beGroupConfigurationRepository;
        $this->exportBeGroupToConfigurationFile = $exportBeGroupToConfigurationFile;
    }

    // Not a good way - in case of overrule a synch would be useless. We need a more detailed strategy here...
    // Maybe ignore overrule here and use only extend mode?
    public function mergeAndExportGroups(Source $source): void
    {
        // Export local groups
        $this->bulkExportBeGroupsToConfigurationFiles->exportGroups();

        // synchronize production groups
        $this->synchronizeBeGroupsFromProduction->syncBeGroups($source);

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
        $this->bulkExportBeGroupsToConfigurationFiles->exportGroups();
    }

    public function mergeAndExportGroup(Source $source, Identifier $identifier): void
    {
        $this->exportBeGroupToConfigurationFile->exportGroup((string)$identifier);

        $this->synchronizeBeGroupsFromProduction->syncBeGroup($source, $identifier);

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

        $this->exportBeGroupToConfigurationFile->exportGroup((string)$identifier);
    }
}
