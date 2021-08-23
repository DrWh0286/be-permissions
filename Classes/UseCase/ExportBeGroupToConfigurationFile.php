<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\UseCase;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Repository\BeGroupRepositoryInterface;
use Pluswerk\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Core\Environment;

final class ExportBeGroupToConfigurationFile
{
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(BeGroupRepositoryInterface $beGroupRepository)
    {
        $this->beGroupRepository = $beGroupRepository;
    }

    public function exportGroup(string $identifier)
    {
        $identifier = new Identifier($identifier);
        $group = $this->beGroupRepository->findOneByIdentifier($identifier);
        $configPath = Environment::getConfigPath();
        $configuration = BeGroupConfiguration::createFromBeGroup($group, $configPath);
        $configuration->write();
    }
}
