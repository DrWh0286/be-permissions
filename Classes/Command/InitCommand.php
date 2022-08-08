<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Command;

use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\UseCase\ExportBeGroupsToConfigurationFile;
use SebastianHofer\BePermissions\Value\DeployProcessing;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InitCommand extends Command
{
    private BeGroupRepositoryInterface $beGroupRepository;
    private ExportBeGroupsToConfigurationFile $exportBeGroupsToConfigurationFile;

    public function __construct(
        BeGroupRepositoryInterface $beGroupRepository,
        ExportBeGroupsToConfigurationFile $exportBeGroupsToConfigurationFile
    ) {
        parent::__construct('Init');
        $this->beGroupRepository = $beGroupRepository;
        $this->exportBeGroupsToConfigurationFile = $exportBeGroupsToConfigurationFile;
    }

    protected function configure(): void
    {
        $this->setDescription('Initializes all existing groups as code manages with given deploy processing.');
        $this->addArgument(
            'deploy_processing',
            InputArgument::OPTIONAL,
            'The deploy processing to init the groups with. Default \'extend\'.',
            (string)DeployProcessing::createExtend()
        );

        $this->addOption('export', 'e');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $deployProcessing = DeployProcessing::createExtend();

        $argumentDeployProcessing = $input->getArgument('deploy_processing');
        if (is_string($argumentDeployProcessing) && $argumentDeployProcessing !== '') {
            $deployProcessing = DeployProcessing::createFromDBValue($argumentDeployProcessing);
        }

        $this->beGroupRepository->initAllGroupsAsCodeManages($deployProcessing);

        if ($input->getOption('export')) {
            $this->exportBeGroupsToConfigurationFile->exportGroups();
        }

        return Command::SUCCESS;
    }
}
