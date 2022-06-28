<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\UseCase\SynchronizeBeGroupsFromProduction;
use Pluswerk\BePermissions\Value\Identifier;
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
        $this->setDescription('Synchronizes be_groups records from production system. Overrules you local database records!');
        $this->addArgument('identifier', InputArgument::OPTIONAL, 'To synchronize only a special group with the give identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->hasArgument('identifier')) {
            $idString = $input->getArgument('identifier');

            if (is_string($idString)) {
                $identifier = new Identifier($idString);
                $this->synchronizeBeGroupsFromProduction->syncBeGroup($identifier);
            } else {
                $output->writeln('Identifier is not a string!');
                return Command::FAILURE;
            }
        } else {
            $this->synchronizeBeGroupsFromProduction->syncBeGroups();
        }

        return Command::SUCCESS;
    }
}