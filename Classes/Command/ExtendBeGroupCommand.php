<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "be_permissions".
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

use SebastianHofer\BePermissions\UseCase\ExtendOrCreateBeGroupByConfigurationFile;
use SebastianHofer\BePermissions\Value\Identifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExtendBeGroupCommand extends Command
{
    private ExtendOrCreateBeGroupByConfigurationFile $extendBeGroupByConfigurationFile;

    public function __construct(ExtendOrCreateBeGroupByConfigurationFile $extendBeGroupByConfigurationFile)
    {
        parent::__construct('ExportBeGroups');
        $this->extendBeGroupByConfigurationFile = $extendBeGroupByConfigurationFile;
    }

    protected function configure(): void
    {
        $this->setDescription('Extends or creates a be_group based on stored yaml file.');
        $this->addArgument('identifier', InputArgument::REQUIRED, 'The group identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifierString = $input->getArgument('identifier');

        if (!is_string($identifierString)) {
            throw new \RuntimeException('identifier argument mus be a string!');
        }

        $this->extendBeGroupByConfigurationFile->extendGroup(new Identifier($identifierString));

        return Command::SUCCESS;
    }
}
