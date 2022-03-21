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

    public function findAllForBulkExport(): array
    {
        $connection = $this->getConnection();

        $rows = $connection->select(
            ['*'],
            'be_groups',
            ['bulk_export' => 1]
        )->fetchAllAssociative();

        $beGroups = [];

        /** @var array<string> $row */
        foreach ($rows as $row) {
            if (is_array($row) && !empty($row) && isset($row['identifier']) && !empty($row['identifier'])) {
                $collection = $this->beGroupFieldCollectionBuilder->buildFromDatabaseValues($row);
                $identifier = new Identifier($row['identifier']);

                $beGroups[] = new BeGroup($identifier, $collection);
            }
        }

        return $beGroups;
    }

    private function getConnection(): Connection
    {
        /** @phpstan-ignore-next-line */
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('be_groups');
    }
}
