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

namespace SebastianHofer\BePermissions\Repository;

use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BeGroupRepository implements BeGroupRepositoryInterface
{
    private BeGroupFieldCollectionBuilder $beGroupFieldCollectionBuilder;

    public function __construct(BeGroupFieldCollectionBuilder $beGroupFieldCollectionBuilder)
    {
        $this->beGroupFieldCollectionBuilder = $beGroupFieldCollectionBuilder;
    }

    public function findOneByIdentifier(Identifier $identifier): ?BeGroup
    {
        $connection = $this->getConnection();

        /** @var array<string> $row */
        $row = $connection->select(
            ['*'],
            'be_groups',
            ['identifier' => (string)$identifier],
            [],
            [],
            1
        )->fetchAssociative();

        if (is_array($row) && !empty($row)) {
            $collection = $this->beGroupFieldCollectionBuilder->buildFromDatabaseValues($row);

            return new BeGroup($identifier, $collection);
        }

        return null;
    }

    public function update(BeGroup $beGroup): void
    {
        $connection = $this->getConnection();

        $connection->update(
            'be_groups',
            $beGroup->databaseValues(),
            ['identifier' => (string)$beGroup->identifier()]
        );
    }

    public function add(BeGroup $beGroup): void
    {
        $connection = $this->getConnection();

        $connection->insert(
            'be_groups',
            $beGroup->databaseValues()
        );
    }

    public function findAllCodeManaged(): BeGroupCollection
    {
        $connection = $this->getConnection();

        $rows = $connection->select(
            ['*'],
            'be_groups',
            ['code_managed_group' => 1]
        )->fetchAllAssociative();

        $beGroups = new BeGroupCollection();

        /** @var array<string> $row */
        foreach ($rows as $row) {
            if (is_array($row) && !empty($row) && isset($row['identifier']) && !empty($row['identifier'])) {
                $collection = $this->beGroupFieldCollectionBuilder->buildFromDatabaseValues($row);
                $identifier = new Identifier($row['identifier']);

                $beGroups->add(new BeGroup($identifier, $collection));
            }
        }

        return $beGroups;
    }

    public function addOrUpdateBeGroups(BeGroupCollection $beGroups): void
    {
        /** @var BeGroup $beGroup */
        foreach ($beGroups as $beGroup) {
            $this->addOrUpdateBeGroup($beGroup);
        }
    }

    public function addOrUpdateBeGroup(BeGroup $beGroup): void
    {
        if ($this->isGroupPresent($beGroup)) {
            $this->update($beGroup);
        } else {
            $this->add($beGroup);
        }
    }

    /**
     * @todo Add test!
     */
    public function findOneByUid(int $uid): ?BeGroup
    {
        $connection = $this->getConnection();
        /** @var array<string> $row */
        $row = $connection->select(
            ['*'],
            'be_groups',
            ['uid' => $uid],
            [],
            [],
            1
        )->fetchAssociative();

        if (is_array($row) && !empty($row) && isset($row['identifier']) && !empty($row['identifier'])) {
            $collection = $this->beGroupFieldCollectionBuilder->buildFromDatabaseValues($row);
            $identifier = new Identifier($row['identifier']);
            return new BeGroup($identifier, $collection);
        }

        return null;
    }

    private function getConnection(): Connection
    {
        /** @phpstan-ignore-next-line */
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('be_groups');
    }

    private function isGroupPresent(BeGroup $beGroup): bool
    {
        $connection = $this->getConnection();

        /** @var array<string> $row */
        $row = $connection->select(
            ['*'],
            'be_groups',
            ['identifier' => (string)$beGroup->identifier()],
            [],
            [],
            1
        )->fetchAssociative();

        if (is_array($row) && !empty($row) && $row['identifier'] === (string)$beGroup->identifier()) {
            return true;
        }

        return false;
    }
}
