<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Command;

use SebastianHofer\BePermissions\UseCase\DeployBeGroups;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DeployCommand extends Command
{
    private DeployBeGroups $deployBeGroups;

    public function __construct(DeployBeGroups $deployBeGroups)
    {
        parent::__construct('Deploy');
        $this->deployBeGroups = $deployBeGroups;
    }

    protected function configure(): void
    {
        $this->setDescription('Extends/overrules all existing be_groups yaml configurations into database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deployBeGroups->deployGroups();

        return Command::SUCCESS;
    }
}
