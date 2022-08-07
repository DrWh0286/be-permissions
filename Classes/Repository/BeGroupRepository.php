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

namespace SebastianHofer\BePermissions\Repository;

use Doctrine\DBAL\Driver\Exception;
use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Collection\DuplicateBeGroupFieldException;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\BeGroupFieldInterface;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\InvalidIdentifierException;
use SebastianHofer\BePermissions\Value\Processor\SubGroupValueProcessor;
use SebastianHofer\BePermissions\Value\SubGroup;
use Symfony\Component\Yaml\Yaml;
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

    /**
     * @throws SubGroupNotFoundException|DuplicateBeGroupFieldException|Exception
     */
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

    public function findUidByIdentifier(Identifier $identifier): ?int
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
            $uid = (int)$row['uid'];

            return $uid;
        }

        return null;
    }

    /**
     * @throws GroupNotFullyImportedException|InvalidIdentifierException
     */
    public function update(BeGroup $beGroup): void
    {
        $groupNotFullyImported = false;
        $connection = $this->getConnection();

        try {
            $values = $this->prepareValuesForDatabase($beGroup);
        } catch (GroupDbValuesNotCompletelyResolvedException $exception) {
            $values = $exception->groupDbValues();
            $groupNotFullyImported = true;
        }

        $connection->update(
            'be_groups',
            $values,
            ['identifier' => (string)$beGroup->identifier()]
        );

        if ($groupNotFullyImported) {
            throw new GroupNotFullyImportedException('The group ' . $beGroup->identifier() . ' could not be fully imported', 0, null, $beGroup);
        }
    }

    /**
     * @throws GroupNotFullyImportedException|InvalidIdentifierException
     */
    public function add(BeGroup $beGroup): void
    {
        $groupNotFullyImported = false;
        $connection = $this->getConnection();

        try {
            $values = $this->prepareValuesForDatabase($beGroup);
        } catch (GroupDbValuesNotCompletelyResolvedException $exception) {
            $values = $exception->groupDbValues();
            $groupNotFullyImported = true;
        }

        $connection->insert(
            'be_groups',
            $values
        );

        if ($groupNotFullyImported) {
            throw new GroupNotFullyImportedException(
                'The group ' . $beGroup->identifier() . ' could not be fully imported',
                1234987654,
                null,
                $beGroup
            );
        }
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

    public function findAllCodeManagedRaw(): array
    {
        $connection = $this->getConnection();

        $rows = $connection->select(
            ['*'],
            'be_groups',
            ['code_managed_group' => 1],
            [],
            ['title' => 'asc']
        )->fetchAllAssociative();

        return $rows;
    }

    public function findOneByIdentifierRaw(Identifier $identifier): array
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
            return $row;
        }

        return [];
    }

    /**
     * @throws GroupNotFullyImportedException|InvalidIdentifierException
     */
    public function addOrUpdateBeGroups(BeGroupCollection $beGroups): void
    {
        $notFullyImportedGroups = new BeGroupCollection();
        /** @var BeGroup $beGroup */
        foreach ($beGroups as $beGroup) {
            try {
                $this->addOrUpdateBeGroup($beGroup);
            } catch (GroupNotFullyImportedException $exception) {
                $notFullyImportedGroups->add($beGroup);
            }
        }

        $failedGroups = new BeGroupCollection();
        /** @var BeGroup $notFullyImportedGroup */
        foreach ($notFullyImportedGroups as $notFullyImportedGroup) {
            try {
                $this->addOrUpdateBeGroup($notFullyImportedGroup);
            } catch (GroupNotFullyImportedException $exception) {
                $failedGroups->add($notFullyImportedGroup);
            }
        }

        if (!$failedGroups->isEmpty()) {
            throw new GroupNotFullyImportedException(
                'Some Groups could not be fully imported!',
                1948567695,
                null,
                null,
                $notFullyImportedGroups
            );
        }
    }

    /**
     * @throws GroupNotFullyImportedException|InvalidIdentifierException
     */
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

    public function loadYamlString(Identifier $identifier): string
    {
        $beGroup = $this->findOneByIdentifier($identifier);

        if ($beGroup instanceof BeGroup) {
            $configuration = BeGroupConfiguration::createFromBeGroup($beGroup, '');
            return Yaml::dump($configuration->asArray(), 99, 2);
        }

        return '';
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

    /**
     * @throws GroupDbValuesNotCompletelyResolvedException|InvalidIdentifierException
     * @return string[]
     */
    private function prepareValuesForDatabase(BeGroup $beGroup): array
    {
        $dbValues = [];
        $groupDbValuesNotCompletelyResolved = false;

        $dbValues['identifier'] = (string)$beGroup->identifier();

        /** @var BeGroupFieldInterface $field */
        foreach ($beGroup->beGroupFieldCollection() as $field) {
            if ($field instanceof SubGroup) {
                /** @var SubGroupValueProcessor $processor */
                $processor = GeneralUtility::makeInstance(SubGroupValueProcessor::class);

                try {
                    $fieldValue = $processor->processValuesForDatabase($field);
                } catch (SubGroupNotFoundException $exception) {
                    $fieldValue = $exception->subGroupValue();
                    $groupDbValuesNotCompletelyResolved = true;
                }

                $dbValues[$field->getFieldName()] = $fieldValue;
            } else {
                $dbValues[$field->getFieldName()] = (string)$field;
            }
        }

        if ($groupDbValuesNotCompletelyResolved) {
            throw new GroupDbValuesNotCompletelyResolvedException('Group db values could not be fully fully resolved!', $dbValues);
        }

        return $dbValues;
    }
}
