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

use SebastianHofer\BePermissions\UseCase\OverruleOrCreateBeGroupFromConfigurationFile;
use SebastianHofer\BePermissions\Value\Identifier;
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
