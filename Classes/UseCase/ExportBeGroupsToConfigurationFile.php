<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\UseCase;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepositoryInterface;
use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;
use Pluswerk\BePermissions\Value\Identifier;
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

    public function exportGroup(string $identifier): void
    {
        $identifier = new Identifier($identifier);
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
