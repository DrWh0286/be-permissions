<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Model;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Model\BeGroup
 */
final class BeGroupTest extends UnitTestCase
{
    /**
     * @test
     */
    public function a_group_has_a_title(): void
    {
        $group = $this->createTestGroup();

        $this->assertSame('[PERM] Basic permissions', $group->title());
    }

    /**
     * @test
     */
    public function a_group_has_an_identifier(): void
    {
        $group = $this->createTestGroup();

        $expectedIdentifier = new Identifier('some-identifier');

        $this->assertEquals($expectedIdentifier, $group->identifier());
    }

    /**
     * @test
     */
    public function a_be_group_has_non_exclude_fields(): void
    {
        $group = $this->createTestGroup();

        $this->assertEquals(
            NonExcludeFields::createFromConfigurationArray([
                'pages' => ['media', 'hidden'],
                'tt_content' => ['pages', 'date']
            ]),
            $group->nonExcludeFields()
        );
    }
    
    /**
     * @test
     */
    public function a_be_group_has_explicit_allowdeny(): void
    {
        $group = $this->createTestGroup();

        $this->assertEquals(
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
            ]),
            $group->explicitAllowDeny()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_database_values(): void
    {
        $dbValues = [
            'identifier' => 'some-identifier',
            'title' => 'A title',
            'non_exclude_fields' => 'pages:media,pages:hidden,tt_content:pages,tt_content:date',
            'explicit_allowdeny' => 'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:some_plugina:ALLOW,tt_content:list_type:another_pluginb:ALLOW'
        ];

        $expectedGroup = new BeGroup(
            new Identifier('some-identifier'),
            'A title',
            NonExcludeFields::createFromConfigurationArray([
                'pages' => ['media', 'hidden'],
                'tt_content' => ['pages', 'date']
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

        $this->assertEquals($expectedGroup, BeGroup::createFromDBValues($dbValues));
    }

    /**
     * @test
     */
    public function can_be_prepared_for_writing_to_database(): void
    {
        $group = $this->createTestGroup();

        $this->assertSame(
            [
                'identifier' => 'some-identifier',
                'title' => '[PERM] Basic permissions',
                'non_exclude_fields' => 'pages:media,pages:hidden,tt_content:pages,tt_content:date',
                'explicit_allowdeny' => 'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:some_plugina:ALLOW,tt_content:list_type:another_pluginb:ALLOW'
            ],
            $group->databaseValues()
        );
    }

    /**
     * @test
     */
    public function can_be_overruled_by_be_group_configuration(): void
    {
        $group = $this->createTestGroup();

        $config = [
            'title' => 'Some new group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ],
            'explicit_allowdeny' => [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW'
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW',
                        'third_plugin' => 'ALLOW'
                    ]
                ]
            ]
        ];
        $configuration = BeGroupConfiguration::createFromConfigurationArray($group->identifier(), '', $config);

        $overruledBeGroup = $group->overruleByConfiguration($configuration);

        $expectedBeGroup = new BeGroup($group->identifier(), 'Some new group title',
            NonExcludeFields::createFromConfigurationArray([
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]),
            ExplicitAllowDeny::createFromConfigurationArray([
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'ALLOW'
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW',
                        'third_plugin' => 'ALLOW'
                    ]
                ]
            ]));

        $this->assertEquals($expectedBeGroup, $overruledBeGroup);
    }

    /**
     * @test
     */
    public function can_be_extended_by_configuration(): void
    {
        $group = $this->createTestGroup();
        $config = [
            'title' => 'Some new group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ],
            'explicit_allowdeny' => [
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'DENY'
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW',
                        'third_plugin' => 'ALLOW'
                    ]
                ]
            ]
        ];
        $configuration = BeGroupConfiguration::createFromConfigurationArray($group->identifier(), '', $config);

        $extendedBeGroup = $group->extendByConfiguration($configuration);

        $expectedBeGroup = new BeGroup($group->identifier(), $group->title(),
            NonExcludeFields::createFromConfigurationArray([
                'pages' => [
                    'media',
                    'hidden',
                    'title'
                ],
                'tt_content' => [
                    'pages',
                    'date',
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]),
            ExplicitAllowDeny::createFromConfigurationArray([
                'tt_content' => [
                    'CType' => [
                        'header' => 'ALLOW',
                        'text' => 'DENY',
                        'textpic' => 'ALLOW'
                    ],
                    'list_type' => [
                        'some_plugina' => 'ALLOW',
                        'another_pluginb' => 'ALLOW',
                        'third_plugin' => 'ALLOW'
                    ]
                ]
            ]));

        $this->assertEquals($expectedBeGroup, $extendedBeGroup);
    }

    private function createTestGroup(): BeGroup
    {
        $identifier = new Identifier('some-identifier');
        $nonExcludeFields = NonExcludeFields::createFromConfigurationArray([
            'pages' => ['media', 'hidden'],
            'tt_content' => ['pages', 'date']
        ]);

        $explicitAllowDeny = ExplicitAllowDeny::createFromConfigurationArray(
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

        return new BeGroup($identifier, '[PERM] Basic permissions', $nonExcludeFields, $explicitAllowDeny);
    }
}
