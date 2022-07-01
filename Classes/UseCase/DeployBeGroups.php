<?php

declare(strict_types=1);

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
