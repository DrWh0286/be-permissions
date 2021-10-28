<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Integration\Repository;

use Pluswerk\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Configuration\ConfigurationFileMissingException;
use Pluswerk\BePermissions\Configuration\ExtensionConfiguration;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\BeGroupFieldFactory;
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

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [];
    }

    /**
     * @test
     */
    public function be_group_can_be_written_to_configuration_file(): void //phpcs:ignore
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');
        $collection = new BeGroupFieldCollection();

        $collection->add(NonExcludeFields::createFromYamlConfiguration([
            'pages' => [
                'title',
                'media'
            ]
        ]));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration([
            'tt_content' => [
                'CType' => [
                    'header' => 'ALLOW',
                    'text' => 'ALLOW',
                    'textpic' => 'ALLOW'
                ]
            ]
        ]));
        $collection->add(AllowedLanguages::createFromYamlConfiguration([0,3,5]));

        $beGroup = new BeGroup($identifier, 'Group title', $collection);

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);
        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
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
            ],
            'allowed_languages' => [0,3,5]
        ];

        $actualContent = Yaml::parse(file_get_contents($expectedFilename));

        $this->assertSame($expectedValue, $actualContent);

        $this->cleanup($identifier);
    }

    /**
     * @test
     */
    public function configuration_file_is_updated(): void //phpcs:ignore
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('update-test-identifier');

        $collection = new BeGroupFieldCollection();
        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        ));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration(
            [
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
        ));

        $config = new BeGroupConfiguration($identifier, $configPath, 'some group title', $collection);

        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
        $repository->write($config);

        $collection = new BeGroupFieldCollection();
        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'media',
                    'some_field'
                ]
            ]
        ));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration(
            [
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
        ));

        $config = new BeGroupConfiguration($identifier, $configPath, 'some group title', $collection);
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
    public function configuration_is_loaded_from_existing_config_file(): void //phpcs:ignore
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('existing-config-identifier');
        $config = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($config);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);

        $config = $repository->load($identifier, $configPath);

        $collection = new BeGroupFieldCollection();

        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        ));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration(
            [
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
        ));

        $expected = new BeGroupConfiguration($identifier, $configPath, 'Some group title', $collection);

        $this->assertEquals($expected, $config);
    }

    /**
     * @test
     */
    public function throws_exception_when_file_to_load_does_not_exist(): void //phpcs:ignore
    {
        $configPath = $this->basePath . '/config/be_groups';
        $identifier = new Identifier('non-existing-config-identifier');
        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);

        $this->expectException(ConfigurationFileMissingException::class);

        $repository->load($identifier, $configPath);
    }

    /**
     * @test
     */
    public function no_empty_arrays_are_written_to_configuration_file(): void //phpcs:ignore
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');
        $collection = new BeGroupFieldCollection();

        $collection->add(NonExcludeFields::createFromYamlConfiguration([
            'pages' => [
                'title',
                'media'
            ]
        ]));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration([]));
        $collection->add(AllowedLanguages::createFromYamlConfiguration([]));

        $beGroup = new BeGroup(
            $identifier,
            'Group title',
            $collection
        );

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);

        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
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
