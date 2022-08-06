<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Hook;

use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupRepository;
use SebastianHofer\BePermissions\UseCase\ExportBeGroupsToConfigurationFile;
use TYPO3\CMS\Core\Configuration\Features;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

final class DataHandlerBeGroupsAutomaticExportHook
{
    private Features $features;

    public function __construct(Features $features)
    {
        $this->features = $features;
    }

    /**
     * @param string $status
     * @param string $table
     * @param string|int $id
     * @param array<int|string> $fieldArray
     * @param DataHandler $dataHandler
     * @return void
     */
    public function processDatamap_afterDatabaseOperations(string $status, string $table, $id, array $fieldArray, DataHandler $dataHandler): void //phpcs:ignore
    {
        if ($table === 'be_groups') {
            /** @var BeGroupRepository $repo */
            $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

            if (!MathUtility::canBeInterpretedAsInteger($id) && $status === 'new') {
                $id = (int)$dataHandler->substNEWwithIDs[$id];
            }

            $beGroup = $repo->findOneByUid((int)$id);

            if ($this->automaticExportEnabled($beGroup)) {
                /** @var ExportBeGroupsToConfigurationFile $exportUseCase */
                $exportUseCase = GeneralUtility::makeInstance(ExportBeGroupsToConfigurationFile::class);
                /** @var BeGroup $beGroup */
                $exportUseCase->exportGroup($beGroup->identifier());
            }
        }
    }

    private function automaticExportEnabled(?BeGroup $beGroup): bool
    {
        return $beGroup instanceof BeGroup
            && $beGroup->isCodeManaged()
            && $this->features->isFeatureEnabled('be_permissions.automaticBeGroupsExportWithSave');
    }
}
