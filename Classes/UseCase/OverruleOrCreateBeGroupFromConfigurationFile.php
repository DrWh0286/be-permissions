<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\UseCase;

use SebastianHofer\BePermissions\Configuration\ConfigurationFileMissingException;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepositoryInterface;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\InvalidIdentifierException;
use TYPO3\CMS\Core\Core\Environment;

final class OverruleOrCreateBeGroupFromConfigurationFile
{
    private BeGroupRepositoryInterface $beGroupRepository;
    private BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository;

    public function __construct(BeGroupRepositoryInterface $beGroupRepository, BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository)
    {
        $this->beGroupRepository = $beGroupRepository;
        $this->beGroupConfigurationRepository = $beGroupConfigurationRepository;
    }

    /**
     * @throws ConfigurationFileMissingException
     */
    public function overruleGroup(Identifier $identifier): void
    {
        $configPath = Environment::getConfigPath();

        $beGroupConfiguration = $this->beGroupConfigurationRepository->load($identifier, $configPath);
        $beGroup = $this->beGroupRepository->findOneByIdentifier($identifier);

        if ($beGroup instanceof BeGroup) {
            $updatedBeGroup = $beGroup->overruleByConfiguration($beGroupConfiguration);

            $this->beGroupRepository->update($updatedBeGroup);
        } else {
            $beGroup = new BeGroup($identifier, $beGroupConfiguration->beGroupFieldCollection());

            $this->beGroupRepository->add($beGroup);
        }
    }
}
