<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\UseCase\SynchronizeBeGroupsFromProduction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SyncProdBeGroupsCommand extends Command
{
    private SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction;

    public function __construct(SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction)
    {
        parent::__construct('SyncProdBeGroups');
        $this->synchronizeBeGroupsFromProduction = $synchronizeBeGroupsFromProduction;
    }

    protected function configure(): void
    {
        $this->setDescription('Bulk exports local groups, sync be_groups table from given source (default staging), extends local be_groups and export the result again.');
        $this->addArgument('source', InputArgument::REQUIRED, 'staging');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->synchronizeBeGroupsFromProduction->syncBeGroups();
        return Command::SUCCESS;
    }
}
