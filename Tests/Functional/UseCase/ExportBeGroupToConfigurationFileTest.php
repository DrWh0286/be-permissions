<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Functional\UseCase;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Pluswerk\BePermissions\UseCase\ExportBeGroupToConfigurationFile;

/**
 * @covers \Pluswerk\BePermissions\UseCase\ExportBeGroupToConfigurationFile
 */
final class ExportBeGroupToConfigurationFileTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    /**
     * @test
     */
    public function an_existing_be_group_can_be_exported_to_a_be_group_configuration_file(): void
    {
        $this->importDataSet(__DIR__ . '/Fixtures/be_groups.xml');

        /** @var ExportBeGroupToConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(ExportBeGroupToConfigurationFile::class);

        $useCase->exportGroup('test-group');

        $configPath = Environment::getConfigPath();

        $this->assertFileExists($configPath . '/be_groups/test-group/be_group.yaml');
        $this->assertFileEquals(__DIR__ . '/Fixtures/be_group.yaml', $configPath . '/be_groups/test-group/be_group.yaml');

        // Cleanup
        @unlink($configPath . '/be_groups/test-group/be_group.yaml');
        rmdir($configPath . '/be_groups/test-group');
        rmdir($configPath . '/be_groups');
    }
}
