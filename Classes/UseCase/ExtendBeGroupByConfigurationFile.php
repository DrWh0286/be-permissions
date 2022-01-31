<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\UseCase;

use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepositoryInterface;
use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;
use Pluswerk\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Core\Environment;

final class ExtendBeGroupByConfigurationFile
{
    private BeGroupRepositoryInterface $beGroupRepository;
    private BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository;

    public function __construct(BeGroupRepositoryInterface $beGroupRepository, BeGroupConfigurationRepositoryInterface $beGroupConfigurationRepository)
    {
        $this->beGroupRepository = $beGroupRepository;
        $this->beGroupConfigurationRepository = $beGroupConfigurationRepository;
    }

    public function extendGroup(string $identifier): void
    {
        $identifier = new Identifier($identifier);
        $configPath = Environment::getConfigPath();

        $beGroupConfiguration = $this->beGroupConfigurationRepository->load($identifier, $configPath);
        $beGroup = $this->beGroupRepository->findOneByIdentifier($identifier);

        if ($beGroup instanceof BeGroup) {
            $updatedBeGroup = $beGroup->extendByConfiguration($beGroupConfiguration);

            $this->beGroupRepository->update($updatedBeGroup);
        } else {
            $beGroup = new BeGroup($identifier, $beGroupConfiguration->beGroupFieldCollection());

            $this->beGroupRepository->add($beGroup);
        }
    }
}
