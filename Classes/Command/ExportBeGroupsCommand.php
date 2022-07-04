<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "form_consent".
 *
 * Copyright (C) 2022 Sebastian Hofer <sebastian.hofer@s-hofer.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace SebastianHofer\BePermissions\Command;

use SebastianHofer\BePermissions\UseCase\ExportBeGroupsToConfigurationFile;
use SebastianHofer\BePermissions\Value\Identifier;
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
                $this->exportBeGroupToConfigurationFile->exportGroup(new Identifier($idString));
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
