<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Command;

use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InitIdentifiersCommand extends Command
{
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(BeGroupRepositoryInterface $beGroupRepository)
    {
        parent::__construct('InitIdentifiers');
        $this->beGroupRepository = $beGroupRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Initializes all groups (including not conde managed) with an identifier if it is not set.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->beGroupRepository->initIdentifierIfNecessary();

        return Command::SUCCESS;
    }
}
