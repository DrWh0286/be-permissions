<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\UseCase\ExportBeGroupsToConfigurationFile;
use Pluswerk\BePermissions\Value\Identifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExportBeGroupsCommand extends Command
{
    private ExportBeGroupsToConfigurationFile $exportBeGroupToConfigurationFile;

    public function __construct(ExportBeGroupsToConfigurationFile $exportBeGroupToConfigurationFile)
    {
        parent::__construct('ExportBeGroups');
        $this->exportBeGroupToConfigurationFile = $exportBeGroupToConfigurationFile;
    }

    protected function configure(): void
    {
        $this->setDescription('Exports a be_group to yaml file.');
        $this->addArgument('identifier', InputArgument::OPTIONAL, 'The group identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->hasArgument('identifier') && $input->getArgument('identifier') !== null) {
            $idString = $input->getArgument('identifier');

            if (is_string($idString)) {
                $this->exportBeGroupToConfigurationFile->exportGroup($idString);
            } else {
                $output->writeln('Identifier is not a string!');
                return Command::FAILURE;
            }
        } else {
            $this->exportBeGroupToConfigurationFile->exportGroups();
        }

        return Command::SUCCESS;
    }
}
