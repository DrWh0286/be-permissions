<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\UseCase\ExportBeGroupToConfigurationFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

final class ExportBeGroupsCommand extends Command
{
    private ExportBeGroupToConfigurationFile $exportBeGroupToConfigurationFile;

    public function __construct(ExportBeGroupToConfigurationFile $exportBeGroupToConfigurationFile)
    {
        parent::__construct('ExportBeGroups');
        $this->exportBeGroupToConfigurationFile = $exportBeGroupToConfigurationFile;
    }

    protected function configure()
    {
        $this->setDescription('Exports a be_group to yaml file.');
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The group identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifierString = $input->getArgument('identifier');
        $this->exportBeGroupToConfigurationFile->exportGroup($identifierString);

        return Command::SUCCESS;
    }
}
