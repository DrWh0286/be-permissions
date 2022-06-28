<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\UseCase\MergeWithProductionAndExport;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\Source;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MergeProdBeGroupsCommand extends Command
{
    private MergeWithProductionAndExport $mergeWithProductionAndExport;

    public function __construct(MergeWithProductionAndExport $mergeWithProductionAndExportBeGroupsToConfigurationFiles)
    {
        parent::__construct('SyncProdBeGroups');
        $this->mergeWithProductionAndExport = $mergeWithProductionAndExportBeGroupsToConfigurationFiles;
    }

    protected function configure(): void
    {
        $this->setDescription('Merges be_groups records from production system wth your local records and exports the result.');
        $this->addArgument('source', InputArgument::OPTIONAL, 'From where should the groups be fetched. Possible values: production, staging, testing');
        $this->addArgument('identifier', InputArgument::OPTIONAL, 'To merge and export only a special group with the give identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->hasArgument('source') && is_string($input->getArgument('source'))) {
            $source = new Source($input->getArgument('source'));
        } else {
            $source = new Source();
        }

        if ($input->hasArgument('identifier')) {
            $idString = $input->getArgument('identifier');

            if (is_string($idString)) {
                $identifier = new Identifier($idString);
                $this->mergeWithProductionAndExport->mergeAndExportGroup($source, $identifier);
            } else {
                $output->writeln('Identifier is not a string!');
                return Command::FAILURE;
            }
        } else {
            $this->mergeWithProductionAndExport->mergeAndExportGroups($source);
        }

        return Command::SUCCESS;
    }
}
