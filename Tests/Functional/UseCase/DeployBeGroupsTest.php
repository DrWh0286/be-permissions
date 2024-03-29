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

namespace SebastianHofer\BePermissions\Tests\Functional\UseCase;

use SebastianHofer\BePermissions\UseCase\DeployBeGroups;
use SebastianHofer\BePermissions\UseCase\ExportBeGroupsToConfigurationFile;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \SebastianHofer\BePermissions\UseCase\DeployBeGroups
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

        /** @var string[] $deployedGroupE */
        $deployedGroupE = $connection->select(
            ['subgroup'],
            'be_groups',
            ['uid' => 2],
            [],
            [],
            1
        )->fetchAssociative();

        $this->assertSame('3,6', $deployedGroupE['subgroup']);
    }

    private function getConnection(): Connection
    {
        /** @phpstan-ignore-next-line */
        return GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('be_groups');
    }
}
