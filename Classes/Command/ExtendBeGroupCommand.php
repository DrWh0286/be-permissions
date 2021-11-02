<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\UseCase\ExtendBeGroupByConfigurationFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExtendBeGroupCommand extends Command
{
    private ExtendBeGroupByConfigurationFile $extendBeGroupByConfigurationFile;

    public function __construct(ExtendBeGroupByConfigurationFile $extendBeGroupByConfigurationFile)
    {
        parent::__construct('ExportBeGroups');
        $this->extendBeGroupByConfigurationFile = $extendBeGroupByConfigurationFile;
    }

    protected function configure(): void
    {
        $this->setDescription('Extends a be_group based on stored yaml file.');
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The group identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifierString = $input->getArgument('identifier');
        $this->extendBeGroupByConfigurationFile->extendGroup($identifierString);

        return Command::SUCCESS;
    }
}
