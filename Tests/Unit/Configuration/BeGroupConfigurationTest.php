<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Configuration;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Configuration\ConfigurationFileMissingException;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class BeGroupConfigurationTest extends UnitTestCase
{
    protected $resetSingletonInstances = true;
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = __DIR__ . '/Fixtures';
    }

    /**
     * @test
     */
    public function configuration_can_be_written_to_a_file(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('some-identifier');
        $config = [
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        ];

        $config = new BeGroupConfiguration($identifier, $configPath, $config);

        $config->write();

        $expectedFilename = $configPath . '/' . $identifier . '/be_group.yaml';
        $this->assertFileExists($expectedFilename);
        $expectedValue = [
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        ];

        $actualContent = Yaml::parse(file_get_contents($expectedFilename));

        $this->assertSame($expectedValue, $actualContent);

        $this->cleanup($identifier);
    }

    /**
     * @test
     */
    public function configuration_file_is_updated(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('update-test-identifier');
        $config = [
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        ];

        $config = new BeGroupConfiguration($identifier, $configPath, $config);
        $config->write();


        $updateConfig = [
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media',
                    'some_field'
                ]
            ]
        ];

        $config = new BeGroupConfiguration($identifier, $configPath, $updateConfig);
        $config->write();

        $expectedValue = [
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media',
                    'some_field'
                ]
            ]
        ];

        $expectedFilename = $configPath . '/' . $identifier . '/be_group.yaml';
        $actualContent = Yaml::parse(file_get_contents($expectedFilename));

        $this->assertSame($expectedValue, $actualContent);

        $this->cleanup($identifier);
    }

    /**
     * @test
     */
    public function configuration_is_loaded_from_existing_config_file(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('existing-config-identifier');

        $config = BeGroupConfiguration::load($identifier, $configPath);

        $expected = [
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        ];

        $this->assertSame($expected, $config->rawConfiguration());
    }

    /**
     * @test
     */
    public function throws_exception_when_file_to_load_does_not_exist(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('non-existing-config-identifier');

        $this->expectException(ConfigurationFileMissingException::class);
        $this->expectExceptionMessage(
            'No configuration file \'/var/www/html/packages/be_permissions/Tests/Unit/Configuration/Fixtures/config/non-existing-config-identifier/be_group.yaml\' found!'
        );

        BeGroupConfiguration::load($identifier, $configPath);
    }

    /**
     * @test
     */
    public function can_be_created_from_be_group_model(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');
        $beGroup = new BeGroup(
            $identifier,
            'Group title',
            [
            'pages' => [
                'title',
                'media'
            ]
        ]);

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);

        $expectedConfig = new BeGroupConfiguration(
            $identifier,
            $configPath,
            [
                'title' => 'Group title',
                'non_exclude_fields' => [
                    'pages' => [
                        'title',
                        'media'
                    ]
                ]
            ]
        );

        $this->assertEquals($expectedConfig, $config);
    }

    private function cleanup(Identifier $identifier)
    {
        @unlink($this->basePath . '/config/' . $identifier . '/be_group.yaml');
        rmdir($this->basePath . '/config/' . $identifier);
    }
}
