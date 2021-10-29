<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Repository;

use Pluswerk\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;
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

    public function findOneByIdentifier(Identifier $identifier): BeGroup
    {
        $connection = $this->getConnection();

        /** @phpstan-ignore-next-line */
        $row = $connection->select(
            ['*'],
            'be_groups',
            ['identifier' => (string)$identifier],
            [],
            [],
            1
        )->fetchAssociative();

        $collection = $this->beGroupFieldCollectionBuilder->buildFromDatabaseValues($row);

        return new BeGroup($identifier, $collection);
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
        /** @phpstan-ignore-next-line */
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('be_groups');
    }
}
