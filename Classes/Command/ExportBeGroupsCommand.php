<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

final class ExportBeGroupsCommand extends Command
{
    private BackendUserGroupRepository $backendUserGroupRepository;

    public function __construct()
    {
        parent::__construct('ExportBeGroups');
        $this->backendUserGroupRepository = GeneralUtility::makeInstance(ObjectManager::class)->get(BackendUserGroupRepository::class);
    }

    protected function configure()
    {
        $this->setDescription('Exports a be_group to yaml file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $beGroup = $this->backendUserGroupRepository->findByUid(5);
        $file = new BeGroupConfiguration($beGroup);
        $file->write();
        return Command::SUCCESS;
    }
}
