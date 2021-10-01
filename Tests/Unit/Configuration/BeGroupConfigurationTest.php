<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Configuration;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Configuration\ConfigurationFileMissingException;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Symfony\Component\Yaml\Yaml;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
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
    public function can_be_created_from_be_group_model(): void
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
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW'
                    ]
                ]
            ])
        );

        $config = BeGroupConfiguration::createFromBeGroup($beGroup, $configPath);

        $expectedConfig = BeGroupConfiguration::createFromConfigurationArray(
            $identifier,
            $configPath,
            [
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
                ]
            ]
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
    public function holds_non_exclude_fields(): void
    {
        $config = $this->getTestConfiguration();

        $this->assertEquals(
            NonExcludeFields::createFromConfigurationArray([
                'pages' => [
                    'title',
                    'media'
                ]
            ]),
            $config->nonExcludeFields()
        );
    }

    /**
     * @test
     */
    public function holds_explicit_allow_deny(): void
    {
        $config = $this->getTestConfiguration();

        $configArray = [
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
        ];

        $explicitAllowDeny = ExplicitAllowDeny::createFromConfigurationArray($configArray);

        $this->assertEquals($explicitAllowDeny, $config->explicitAllowDeny());
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
            ]
        ], $config->asArray());
    }

    private function getTestConfiguration(): BeGroupConfiguration
    {
        $configPath = $this->basePath . '/config';
        $identifier = new Identifier('from-be-group');
        $config = BeGroupConfiguration::createFromConfigurationArray(
            $identifier,
            $configPath,
            [
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
                ]
            ]
        );

        return $config;
    }
}
