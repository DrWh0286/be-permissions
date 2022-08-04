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

use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use SebastianHofer\BePermissions\UseCase\ExportBeGroupsToConfigurationFile;

/**
 * @covers \SebastianHofer\BePermissions\UseCase\ExportBeGroupsToConfigurationFile
 * @uses \TYPO3\CMS\Core\Core\Environment
 * @uses \TYPO3\CMS\Core\Utility\GeneralUtility
 * @uses \SebastianHofer\BePermissions\Configuration\BeGroupConfiguration
 * @uses \SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepository
 * @uses \SebastianHofer\BePermissions\Repository\BeGroupRepository
 * @uses \SebastianHofer\BePermissions\Value\Identifier
 */
final class ExportBeGroupsToConfigurationFileTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    /**
     * @test
     */
    public function an_existing_be_group_can_be_exported_to_a_be_group_configuration_file(): void //phpcs:ignore
    {
        $this->importDataSet(__DIR__ . '/Fixtures/be_groups.xml');

        /** @var ExportBeGroupsToConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(ExportBeGroupsToConfigurationFile::class);

        $useCase->exportGroup(new Identifier('test-group'));

        $configPath = Environment::getConfigPath();

        $this->assertFileExists($configPath . '/be_groups/test-group/be_group.yaml');
        $this->assertFileEquals(__DIR__ . '/Fixtures/be_group.yaml', $configPath . '/be_groups/test-group/be_group.yaml');

        // Cleanup
        @unlink($configPath . '/be_groups/test-group/be_group.yaml');
        rmdir($configPath . '/be_groups/test-group');
        rmdir($configPath . '/be_groups');
    }

    /**
     * @test
     */
    public function all_code_managed_groups_are_exported_to_yaml_configuration(): void // phpcs:ignore
    {
        $this->importDataSet(__DIR__ . '/Fixtures/bulk_be_groups.xml');

        /** @var ExportBeGroupsToConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(ExportBeGroupsToConfigurationFile::class);

        $useCase->exportGroups();

        $configPath = Environment::getConfigPath();

        $this->assertFileExists($configPath . '/be_groups/test-group-a/be_group.yaml');
        $this->assertFileExists($configPath . '/be_groups/test-group-b/be_group.yaml');
        $this->assertFileExists($configPath . '/be_groups/test-group-d/be_group.yaml');
        $this->assertFileEquals(__DIR__ . '/Fixtures/be_group-a.yaml', $configPath . '/be_groups/test-group-a/be_group.yaml');
        $this->assertFileEquals(__DIR__ . '/Fixtures/be_group-b.yaml', $configPath . '/be_groups/test-group-b/be_group.yaml');
        $this->assertFileEquals(__DIR__ . '/Fixtures/be_group-d.yaml', $configPath . '/be_groups/test-group-d/be_group.yaml');

        // Cleanup
        @unlink($configPath . '/be_groups/test-group-a/be_group.yaml');
        @unlink($configPath . '/be_groups/test-group-b/be_group.yaml');
        @unlink($configPath . '/be_groups/test-group-d/be_group.yaml');
        rmdir($configPath . '/be_groups/test-group-a');
        rmdir($configPath . '/be_groups/test-group-b');
        rmdir($configPath . '/be_groups/test-group-d');
        rmdir($configPath . '/be_groups');
    }
}
