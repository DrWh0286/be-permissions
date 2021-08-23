<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Repository;

use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BeGroupRepository implements BeGroupRepositoryInterface
{
    public function findOneByIdentifier(Identifier $identifier): BeGroup
    {
        $connection = $this->getConnection();

        $row = $connection->select(
            ['identifier', 'title', 'non_exclude_fields'],
            'be_groups',
            ['identifier' => (string)$identifier],
            [],
            [],
            1
        )->fetchAllAssociative();

        return BeGroup::createFromDBValues($row);
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

    private function getConnection(): Connection
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('be_groups');
    }
}
