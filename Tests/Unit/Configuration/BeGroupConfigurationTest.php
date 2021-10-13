<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Configuration;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Configuration\BeGroupConfiguration
 * @uses \Pluswerk\BePermissions\Model\BeGroup
 * @uses \Pluswerk\BePermissions\Value\AllowedLanguages
 * @uses \Pluswerk\BePermissions\Value\ExplicitAllowDeny
 * @uses \Pluswerk\BePermissions\Value\Identifier
 * @uses \Pluswerk\BePermissions\Value\NonExcludeFields
 */
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
    public function holds_an_identifier(): void
    {
        $config = $this->getTestConfiguration();

        $this->assertEquals(new Identifier('from-be-group'), $config->identifier());
    }

    /**
     * @test
     */
    public function holds_a_config_path(): void
    {
        $config = $this->getTestConfiguration();

        $this->assertSame($this->basePath . '/config', $config->configPath());
    }

    /**
     * @test
     */
    public function can_be_created_from_be_group_model(): void
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');

        $collection = new BeGroupFieldCollection();

        $collection->add(NonExcludeFields::createFromConfigurationArray([
            'pages' => [
                'title',
                'media'
            ]
        ]));
        $collection->add(ExplicitAllowDeny::createFromConfigurationArray([
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
        ]));
        $collection->add(AllowedLanguages::createFromConfigurationArray([0,3,5]));

        $beGroup = new BeGroup($identifier, 'Group title', $collection);

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);

        $collection = new BeGroupFieldCollection();
        $nonExcludeFields = NonExcludeFields::createFromConfigurationArray(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        );
        $explicitAllowdeny = ExplicitAllowDeny::createFromConfigurationArray(
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
        );
        $allowedLanguages = AllowedLanguages::createFromConfigurationArray([0,3,5]);

        $collection->add($nonExcludeFields);
        $collection->add($explicitAllowdeny);
        $collection->add($allowedLanguages);

        $expectedConfig = new BeGroupConfiguration(
            $identifier,
            $configPath,
            'Group title',
            $collection
        );

        $this->assertEquals($expectedConfig, $config);
    }

    /**
     * @test
     */
    public function holds_the_group_title(): void
    {
        $conf = $this->getTestConfiguration();

        $this->assertSame('Group title', $conf->title());
    }

    /**
     * @test
     */
    public function holds_a_be_group_field_collection(): void
    {
        $config = $this->getTestConfiguration();

        $expectedCollection = new BeGroupFieldCollection();
        $nonExcludeFields = NonExcludeFields::createFromConfigurationArray(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        );
        $explicitAllowdeny = ExplicitAllowDeny::createFromConfigurationArray(
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
        );
        $allowedLanguages = AllowedLanguages::createFromConfigurationArray([0,3,5]);

        $expectedCollection->add($nonExcludeFields);
        $expectedCollection->add($explicitAllowdeny);
        $expectedCollection->add($allowedLanguages);

        $this->assertEquals($expectedCollection, $config->beGroupFieldCollection());
    }

    /**
     * @test
     */
    public function can_be_fetched_as_configuration_array_for_writing(): void
    {
        $config = $this->getTestConfiguration();

        $this->assertSame([
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
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW'
                    ]
                ]
            ],
            'allowed_languages' => [0,3,5]
        ], $config->asArray());
    }

    private function getTestConfiguration(): BeGroupConfiguration
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');

        $collection = new BeGroupFieldCollection();
        $nonExcludeFields = NonExcludeFields::createFromConfigurationArray(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        );
        $explicitAllowdeny = ExplicitAllowDeny::createFromConfigurationArray(
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
        );
        $allowedLanguages = AllowedLanguages::createFromConfigurationArray([0,3,5]);

        $collection->add($nonExcludeFields);
        $collection->add($explicitAllowdeny);
        $collection->add($allowedLanguages);
        $config = new BeGroupConfiguration(
            $identifier,
            $configPath,
            'Group title',
            $collection
        );

        return $config;
    }
}
