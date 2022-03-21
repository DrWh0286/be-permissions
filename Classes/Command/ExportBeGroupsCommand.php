<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\UseCase\ExportBeGroupToConfigurationFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExportBeGroupsCommand extends Command
{
    private ExportBeGroupToConfigurationFile $exportBeGroupToConfigurationFile;

    public function __construct(ExportBeGroupToConfigurationFile $exportBeGroupToConfigurationFile)
    {
        parent::__construct('ExportBeGroups');
        $this->exportBeGroupToConfigurationFile = $exportBeGroupToConfigurationFile;
    }

    protected function configure(): void
    {
        $this->setDescription('Exports a be_group to yaml file.');
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The group identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifierString = $input->getArgument('identifier');

        if (!is_string($identifierString)) {
            throw new \RuntimeException('identifier argument mus be a string!');
        }

        $this->exportBeGroupToConfigurationFile->exportGroup($identifierString);

        return Command::SUCCESS;
    }
}
