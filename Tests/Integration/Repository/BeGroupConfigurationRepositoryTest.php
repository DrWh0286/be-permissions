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

namespace SebastianHofer\BePermissions\Tests\Integration\Repository;

use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Configuration\ConfigurationFileMissingException;
use SebastianHofer\BePermissions\Configuration\ExtensionConfiguration;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepository;
use SebastianHofer\BePermissions\Value\AllowedLanguages;
use SebastianHofer\BePermissions\Value\BeGroupFieldFactory;
use SebastianHofer\BePermissions\Value\ExplicitAllowDeny;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\NonExcludeFields;
use SebastianHofer\BePermissions\Value\Title;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepository
 * @uses \SebastianHofer\BePermissions\Configuration\BeGroupConfiguration
 * @uses \SebastianHofer\BePermissions\Configuration\ConfigurationFileMissingException
 * @uses \SebastianHofer\BePermissions\Model\BeGroup
 * @uses \SebastianHofer\BePermissions\Value\AllowedLanguages
 * @uses \SebastianHofer\BePermissions\Value\ExplicitAllowDeny
 * @uses \SebastianHofer\BePermissions\Value\Identifier
 * @uses \SebastianHofer\BePermissions\Value\NonExcludeFields
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

        $collection->add(Title::createFromYamlConfiguration('Group title'));
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

        $beGroup = new BeGroup($identifier, $collection);

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);
        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
        $repository->write($config);

        $expectedFilename = $configPath . '/be_groups/' . $identifier . '/be_group.yaml';
        $this->assertFileExists($expectedFilename);
        $expectedValue = [
            'allowed_languages' => [0,3,5],
            'explicit_allowdeny' => [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ]
                ]
            ],
            'non_exclude_fields' => [
                'pages' => [
                    'media',
                    'title'
                ]
            ],
            'title' => 'Group title'
        ];

        $expectedJsonString = file_get_contents($expectedFilename) ?: '';
        $actualContent = Yaml::parse($expectedJsonString);

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
        $collection->add(Title::createFromYamlConfiguration('some group title'));
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

        $config = new BeGroupConfiguration($identifier, $configPath, $collection);

        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
        $repository->write($config);

        $collection = new BeGroupFieldCollection();
        $collection->add(Title::createFromYamlConfiguration('some group title'));
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

        $config = new BeGroupConfiguration($identifier, $configPath, $collection);
        $repository->write($config);

        $expectedValue = [
            'explicit_allowdeny' => [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW',
                        'textpic' => 'ALLOW'
                    ],
                    'list_type' => [
                        'another_pluginb' => 'ALLOW',
                        'some_plugina' => 'ALLOW'
                    ]
                ]
            ],
            'non_exclude_fields' => [
                'pages' => [
                    'media',
                    'some_field',
                    'title'
                ]
            ],
            'title' => 'some group title',
        ];

        $filename = $configPath . '/be_groups/' . $identifier . '/be_group.yaml';
        $actualJsonString = file_get_contents($filename) ?: '';
        $actualContent = Yaml::parse($actualJsonString);

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

        $collection->add(Title::createFromYamlConfiguration('Some group title'));
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

        $expected = new BeGroupConfiguration($identifier, $configPath, $collection);

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
    public function also_empty_arrays_are_written_to_configuration_file(): void //phpcs:ignore
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');
        $collection = new BeGroupFieldCollection();

        $collection->add(Title::createFromYamlConfiguration('Group title'));
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
            'allowed_languages' => [],
            'explicit_allowdeny' => [],
            'non_exclude_fields' => [
                'pages' => [
                    'media',
                    'title'
                ]
            ],
            'title' => 'Group title'
        ];

        $actualJsonString = file_get_contents($expectedFilename) ?: '';
        $actualContent = Yaml::parse($actualJsonString);

        $this->assertSame($expectedValue, $actualContent);

        $this->cleanup($identifier);
    }

    /**
     * @test
     */
    public function with_no_exported_be_groups_an_empty_collection_is_returned_from_load_all(): void //phpcs:ignore
    {
        if (file_exists(Environment::getConfigPath() . '/be_groups')) {
            rmdir(Environment::getConfigPath() . '/be_groups');
        }

        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);

        $resultingCollection = $repository->loadAll(Environment::getConfigPath());

        $this->assertTrue($resultingCollection->isEmpty());
    }

    private function cleanup(Identifier $identifier): void
    {
        @unlink($this->basePath . '/config/be_groups/' . $identifier . '/be_group.yaml');
        rmdir($this->basePath . '/config/be_groups/' . $identifier);
    }
}
