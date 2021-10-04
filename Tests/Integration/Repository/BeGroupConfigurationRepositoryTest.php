<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Integration\Repository;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Configuration\ConfigurationFileMissingException;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Symfony\Component\Yaml\Yaml;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository
 * @uses \Pluswerk\BePermissions\Configuration\BeGroupConfiguration
 * @uses \Pluswerk\BePermissions\Configuration\ConfigurationFileMissingException
 * @uses \Pluswerk\BePermissions\Model\BeGroup
 * @uses \Pluswerk\BePermissions\Value\AllowedLanguages
 * @uses \Pluswerk\BePermissions\Value\ExplicitAllowDeny
 * @uses \Pluswerk\BePermissions\Value\Identifier
 * @uses \Pluswerk\BePermissions\Value\NonExcludeFields
 * @uses \Symfony\Component\Yaml\Yaml
 */
final class BeGroupConfigurationRepositoryTest extends UnitTestCase
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
    public function be_group_can_be_written_to_configuration_file(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');
        $beGroup = new BeGroup(
            $identifier,
            'Group title',
            NonExcludeFields::createFromConfigurationArray([
                'pages' => [
                    'title',
                    'media'
                ]
            ]),
            ExplicitAllowDeny::createFromConfigurationArray([
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ]
                ]
            ]),
            AllowedLanguages::createFromConfigurationArray([0,3,5])
        );

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);
        $repository = new BeGroupConfigurationRepository();
        $repository->write($config);

        $expectedFilename = $configPath . '/be_groups/' . $identifier . '/be_group.yaml';
        $this->assertFileExists($expectedFilename);
        $expectedValue = [
            'title' => 'Group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ],
            'explicit_allowdeny' => [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ]
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
            'title' => 'some group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ],
            'explicit_allowdeny' => [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW'
                    ]
                ]
            ]
        ];

        $config = BeGroupConfiguration::createFromConfigurationArray($identifier, $configPath, $config);
        $repository = new BeGroupConfigurationRepository();
        $repository->write($config);


        $updateConfig = [
            'title' => 'some group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media',
                    'some_field'
                ]
            ],
            'explicit_allowdeny' => [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW'
                    ]
                ]
            ]
        ];

        $config = BeGroupConfiguration::createFromConfigurationArray($identifier, $configPath, $updateConfig);
        $repository->write($config);

        $expectedValue = [
            'title' => 'some group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media',
                    'some_field'
                ]
            ],
            'explicit_allowdeny' => [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW'
                    ]
                ]
            ]
        ];

        $expectedFilename = $configPath . '/be_groups/' . $identifier . '/be_group.yaml';
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
        $repository = new BeGroupConfigurationRepository();

        $config = $repository->load($identifier, $configPath);

        $expected = BeGroupConfiguration::createFromConfigurationArray($identifier, $configPath, [
            'title' => 'Some group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ],
            'explicit_allowdeny' => [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW'
                    ]
                ]
            ]
        ]);

        $this->assertEquals($expected, $config);
    }

    /**
     * @test
     */
    public function throws_exception_when_file_to_load_does_not_exist(): void
    {
        $configPath = $this->basePath . '/config/be_groups';
        $identifier = new Identifier('non-existing-config-identifier');
        $repository = new BeGroupConfigurationRepository();

        $this->expectException(ConfigurationFileMissingException::class);

        $repository->load($identifier, $configPath);
    }

    /**
     * @test
     */
    public function no_empty_arrays_are_written_to_configuration_file(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');
        $beGroup = new BeGroup(
            $identifier,
            'Group title',
            NonExcludeFields::createFromConfigurationArray([
                'pages' => [
                    'title',
                    'media'
                ]
            ]),
            ExplicitAllowDeny::createFromConfigurationArray([]),
            AllowedLanguages::createFromConfigurationArray([])
        );

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);
        $repository = new BeGroupConfigurationRepository();
        $repository->write($config);

        $expectedFilename = $configPath . '/be_groups/' . $identifier . '/be_group.yaml';
        $this->assertFileExists($expectedFilename);
        $expectedValue = [
            'title' => 'Group title',
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

    private function cleanup(Identifier $identifier)
    {
        @unlink($this->basePath . '/config/be_groups/' . $identifier . '/be_group.yaml');
        rmdir($this->basePath . '/config/be_groups/' . $identifier);
    }
}
