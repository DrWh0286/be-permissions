<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\UseCase\BulkExportBeGroupsToConfigurationFiles;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BulkExportBeGroupsCommand extends Command
{
    private BulkExportBeGroupsToConfigurationFiles $bulkExportBeGroupsToConfigurationFiles;

    public function __construct(BulkExportBeGroupsToConfigurationFiles $bulkExportBeGroupsToConfigurationFiles)
    {
        parent::__construct('BulkExportBeGroups');
        $this->bulkExportBeGroupsToConfigurationFiles = $bulkExportBeGroupsToConfigurationFiles;
    }

    protected function configure(): void
    {
        $this->setDescription('Exports all be_groups, which are enabled for bulk export to their yaml files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bulkExportBeGroupsToConfigurationFiles->exportGroups();

        return Command::SUCCESS;
    }
}
