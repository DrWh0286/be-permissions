<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Configuration;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\DeployProcessing;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Pluswerk\BePermissions\Value\Title;
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
    public function holds_an_identifier(): void //phpcs:ignore
    {
        $config = $this->getTestConfiguration();

        $this->assertEquals(new Identifier('from-be-group'), $config->identifier());
    }

    /**
     * @test
     */
    public function holds_a_config_path(): void //phpcs:ignore
    {
        $config = $this->getTestConfiguration();

        $this->assertSame($this->basePath . '/config', $config->configPath());
    }

    /**
     * @test
     */
    public function can_be_created_from_be_group_model(): void //phpcs:ignore
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
                ],
                'list_type' => [
                    'some_plugina' => 'ALLOW',
                    'another_pluginb' => 'ALLOW'
                ]
            ]
        ]));
        $collection->add(AllowedLanguages::createFromYamlConfiguration([0,3,5]));

        $beGroup = new BeGroup($identifier, $collection);

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);

        $collection = new BeGroupFieldCollection();
        $title = Title::createFromYamlConfiguration('Group title');
        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        );
        $explicitAllowdeny = ExplicitAllowDeny::createFromYamlConfiguration(
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
        $allowedLanguages = AllowedLanguages::createFromYamlConfiguration([0,3,5]);

        $collection->add($title);
        $collection->add($nonExcludeFields);
        $collection->add($explicitAllowdeny);
        $collection->add($allowedLanguages);

        $expectedConfig = new BeGroupConfiguration(
            $identifier,
            $configPath,
            $collection
        );

        $this->assertEquals($expectedConfig, $config);
    }

    /**
     * @test
     */
    public function holds_a_be_group_field_collection(): void //phpcs:ignore
    {
        $config = $this->getTestConfiguration();

        $expectedCollection = new BeGroupFieldCollection();
        $title = Title::createFromYamlConfiguration('Group title');
        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        );
        $explicitAllowdeny = ExplicitAllowDeny::createFromYamlConfiguration(
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
        $allowedLanguages = AllowedLanguages::createFromYamlConfiguration([0,3,5]);

        $expectedCollection->add($title);
        $expectedCollection->add($nonExcludeFields);
        $expectedCollection->add($explicitAllowdeny);
        $expectedCollection->add($allowedLanguages);
        $expectedCollection->add(DeployProcessing::createWithDefault());

        $this->assertEquals($expectedCollection, $config->beGroupFieldCollection());
    }

    /**
     * @test
     */
    public function can_be_fetched_as_configuration_array_for_writing(): void //phpcs:ignore
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
            'allowed_languages' => [0,3,5],
            'deploy_processing' => 'extend'
        ], $config->asArray());
    }

    /**
     * @test
     */
    public function deploy_processing_instructions_can_be_fetched(): void // phpcs:ignore
    {
        $config = $this->getTestConfiguration();

        $this->assertTrue($config->getDeploymentProcessing()->isExtend());
    }

    /**
     * @test
     */
    public function default_deployment_processing_is_used_as_fallback(): void // phpcs:ignore
    {
        $identifier = new Identifier('from-be-group');

        $collection = new BeGroupFieldCollection();

        $config = new BeGroupConfiguration(
            $identifier,
            $this->basePath . '/config',
            $collection
        );

        $this->assertTrue($config->getDeploymentProcessing()->isExtend());
    }

    private function getTestConfiguration(): BeGroupConfiguration
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');

        $collection = new BeGroupFieldCollection();
        $title = Title::createFromYamlConfiguration('Group title');
        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        );
        $explicitAllowdeny = ExplicitAllowDeny::createFromYamlConfiguration(
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
        $allowedLanguages = AllowedLanguages::createFromYamlConfiguration([0,3,5]);

        $collection->add($title);
        $collection->add($nonExcludeFields);
        $collection->add($explicitAllowdeny);
        $collection->add($allowedLanguages);
        $collection->add(DeployProcessing::createWithDefault());
        $config = new BeGroupConfiguration(
            $identifier,
            $configPath,
            $collection
        );

        return $config;
    }
}
