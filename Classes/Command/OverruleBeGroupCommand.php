<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Command;

use Pluswerk\BePermissions\UseCase\OverruleOrCreateBeGroupFromConfigurationFile;
use Pluswerk\BePermissions\Value\Identifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class OverruleBeGroupCommand extends Command
{
    private OverruleOrCreateBeGroupFromConfigurationFile $overruleBeGroupFromConfigurationFile;

    public function __construct(OverruleOrCreateBeGroupFromConfigurationFile $overruleBeGroupFromConfigurationFile)
    {
        parent::__construct('ExportBeGroups');
        $this->overruleBeGroupFromConfigurationFile = $overruleBeGroupFromConfigurationFile;
    }

    protected function configure(): void
    {
        $this->setDescription('Overrules or creates a be_group based on stored yaml file. Be careful with this command!');
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The group identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifierString = $input->getArgument('identifier');

        if (!is_string($identifierString)) {
            throw new \RuntimeException('identifier argument mus be a string!');
        }

        $this->overruleBeGroupFromConfigurationFile->overruleGroup(new Identifier($identifierString));

        return Command::SUCCESS;
    }
}
