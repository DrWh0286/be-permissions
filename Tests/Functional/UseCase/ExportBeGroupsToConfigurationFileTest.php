<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Functional\UseCase;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Pluswerk\BePermissions\UseCase\ExportBeGroupsToConfigurationFile;

/**
 * @covers \Pluswerk\BePermissions\UseCase\ExportBeGroupsToConfigurationFile
 * @uses \TYPO3\CMS\Core\Core\Environment
 * @uses \TYPO3\CMS\Core\Utility\GeneralUtility
 * @uses \Pluswerk\BePermissions\Configuration\BeGroupConfiguration
 * @uses \Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository
 * @uses \Pluswerk\BePermissions\Repository\BeGroupRepository
 * @uses \Pluswerk\BePermissions\Value\Identifier
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

        $useCase->exportGroup('test-group');

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
