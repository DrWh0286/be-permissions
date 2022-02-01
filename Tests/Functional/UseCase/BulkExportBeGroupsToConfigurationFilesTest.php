<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Functional\UseCase;

use Pluswerk\BePermissions\UseCase\BulkExportBeGroupsToConfigurationFiles;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \Pluswerk\BePermissions\UseCase\BulkExportBeGroupsToConfigurationFiles
 */
final class BulkExportBeGroupsToConfigurationFilesTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    /**
     * @test
     */
    public function all_groups_with_bulk_export_true_are_exported_to_yaml_configuration(): void // phpcs:ignore
    {
        $this->importDataSet(__DIR__ . '/Fixtures/bulk_be_groups.xml');

        /** @var BulkExportBeGroupsToConfigurationFiles $useCase */
        $useCase = GeneralUtility::makeInstance(BulkExportBeGroupsToConfigurationFiles::class);

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
