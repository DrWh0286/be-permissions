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

use SebastianHofer\BePermissions\UseCase\DeployBeGroups;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DeployCommand extends Command
{
    private DeployBeGroups $deployBeGroups;

    public function __construct(DeployBeGroups $deployBeGroups)
    {
        parent::__construct('Deploy');
        $this->deployBeGroups = $deployBeGroups;
    }

    protected function configure(): void
    {
        $this->setDescription('Extends/overrules all existing be_groups yaml configurations into database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deployBeGroups->deployGroups();

        return Command::SUCCESS;
    }
}
