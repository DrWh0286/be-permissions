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

use SebastianHofer\BePermissions\UseCase\SynchronizeBeGroupsFromProduction;
use SebastianHofer\BePermissions\Value\Identifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SyncProdBeGroupsCommand extends Command
{
    private SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction;

    public function __construct(SynchronizeBeGroupsFromProduction $synchronizeBeGroupsFromProduction)
    {
        parent::__construct('SyncProdBeGroups');
        $this->synchronizeBeGroupsFromProduction = $synchronizeBeGroupsFromProduction;
    }

    protected function configure(): void
    {
        $this->setDescription('Synchronizes be_groups records from production system. Overrules you local database records!');
        $this->addArgument('identifier', InputArgument::OPTIONAL, 'To synchronize only a special group with the give identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->hasArgument('identifier') && $input->getArgument('identifier') !== null) {
            $idString = $input->getArgument('identifier');

            if (is_string($idString)) {
                $identifier = new Identifier($idString);
                $this->synchronizeBeGroupsFromProduction->syncBeGroup($identifier);
            } else {
                $output->writeln('Identifier is not a string!');
                return Command::FAILURE;
            }
        } else {
            $this->synchronizeBeGroupsFromProduction->syncBeGroups();
        }

        return Command::SUCCESS;
    }
}
