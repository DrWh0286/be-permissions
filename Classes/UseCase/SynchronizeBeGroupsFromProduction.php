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

namespace SebastianHofer\BePermissions\UseCase;

use SebastianHofer\BePermissions\Api\Api;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Repository\GroupNotFullyImportedException;
use SebastianHofer\BePermissions\Value\Identifier;

final class SynchronizeBeGroupsFromProduction
{
    private Api $api;
    private BeGroupRepositoryInterface $beGroupRepository;

    public function __construct(Api $api, BeGroupRepositoryInterface $beGroupRepository)
    {
        $this->api = $api;
        $this->beGroupRepository = $beGroupRepository;
    }

    public function syncBeGroups(): void
    {
        $beGroupsFromProd = $this->api->fetchAllCodeManagedBeGroups();
        $this->beGroupRepository->addOrUpdateBeGroups($beGroupsFromProd);
    }

    /**
     * @throws GroupNotFullyImportedException
     */
    public function syncBeGroup(Identifier $identifier): void
    {
        $beGroup = $this->api->fetchBeGroupsByIdentifier($identifier);

        if (!$beGroup instanceof BeGroup) {
            throw new \RuntimeException('No be group found for ' . $identifier . '!');
        }

        $this->beGroupRepository->addOrUpdateBeGroup($beGroup);
    }
}
