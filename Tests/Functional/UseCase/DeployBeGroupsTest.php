<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Functional\UseCase;

use Pluswerk\BePermissions\UseCase\DeployBeGroups;
use Pluswerk\BePermissions\UseCase\ExportBeGroupsToConfigurationFile;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Pluswerk\BePermissions\UseCase\DeployBeGroups
 */
final class DeployBeGroupsTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [];
    }

    /**
     * @test
     */
    public function all_exported_be_groups_are_deployed_to_database_with_stored_deploy_processing_instruction(): void // phpcs:ignore
    {
        $connection = $this->getConnection();

        $this->importDataSet(__DIR__ . '/Fixtures/local_export_be_groups.xml');

        /** @var ExportBeGroupsToConfigurationFile $exportBeGroupsToConfigurationFiles */
        $exportBeGroupsToConfigurationFiles = GeneralUtility::makeInstance(ExportBeGroupsToConfigurationFile::class);
        $exportBeGroupsToConfigurationFiles->exportGroups();

        $connection->truncate('be_groups');
        $this->importDataSet(__DIR__ . '/Fixtures/pre_deploy_be_groups.xml');

        /** @var DeployBeGroups $deployBeGroups */
        $deployBeGroups = GeneralUtility::makeInstance(DeployBeGroups::class);

        $deployBeGroups->deployGroups();

        /** @var string[] $deployedGroupA */
        $deployedGroupA = $connection->select(
            ['non_exclude_fields'],
            'be_groups',
            ['uid' => 1],
            [],
            [],
            1
        )->fetchAssociative();

        $this->assertSame('pages:hidden,pages:title,tt_content:hidden,tt_content:title', $deployedGroupA['non_exclude_fields']);

        /** @var string[] $deployedGroupB */
        $deployedGroupB = $connection->select(
            ['non_exclude_fields'],
            'be_groups',
            ['uid' => 2],
            [],
            [],
            1
        )->fetchAssociative();

        $this->assertSame('pages:hidden,pages:title,tt_content:hidden,tt_content:title', $deployedGroupB['non_exclude_fields']);

        /** @var string[] $deployedGroupD */
        $deployedGroupD = $connection->select(
            ['non_exclude_fields'],
            'be_groups',
            ['uid' => 4],
            [],
            [],
            1
        )->fetchAssociative();

        $this->assertSame('pages:hidden,pages:title,tt_content:title', $deployedGroupD['non_exclude_fields']);

        /** @var string[] $deployedGroupF */
        $deployedGroupF = $connection->select(
            ['non_exclude_fields'],
            'be_groups',
            ['uid' => 5],
            [],
            [],
            1
        )->fetchAssociative();

        $this->assertSame('pages:title,pages:hidden,tt_content:bodytext,tt_content:header', $deployedGroupF['non_exclude_fields']);

        /** @var string[] $deployedGroupE */
        $deployedGroupE = $connection->select(
            ['non_exclude_fields'],
            'be_groups',
            ['uid' => 6],
            [],
            [],
            1
        )->fetchAssociative();

        $this->assertSame('pages:hidden,pages:title,tt_content:hidden', $deployedGroupE['non_exclude_fields']);
    }

    private function getConnection(): Connection
    {
        /** @phpstan-ignore-next-line */
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('be_groups');
    }
}
