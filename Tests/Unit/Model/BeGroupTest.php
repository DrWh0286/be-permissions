<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Model;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Model\BeGroup
 * @uses \Pluswerk\BePermissions\Configuration\BeGroupConfiguration
 * @uses \Pluswerk\BePermissions\Value\AllowedLanguages
 * @uses \Pluswerk\BePermissions\Value\ExplicitAllowDeny
 * @uses \Pluswerk\BePermissions\Value\Identifier
 * @uses \Pluswerk\BePermissions\Value\NonExcludeFields
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
    public function a_be_group_has_allowed_languages(): void
    {
        $group = $this->createTestGroup();

        $this->assertEquals(
            AllowedLanguages::createFromConfigurationArray([0,3,5]),
            $group->allowedLanguages()
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
            'explicit_allowdeny' => 'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:some_plugina:ALLOW,tt_content:list_type:another_pluginb:ALLOW',
            'allowed_languages' => '0,3,5'
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
            ]),
            AllowedLanguages::createFromConfigurationArray([0,3,5])
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
                'explicit_allowdeny' => 'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:some_plugina:ALLOW,tt_content:list_type:another_pluginb:ALLOW',
                'allowed_languages' => '0,3,5'
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
            ],
            'allowed_languages' => [1,2,4]
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
            ]),
            AllowedLanguages::createFromConfigurationArray([1,2,4])
        );

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
            ],
            'allowed_languages' => [3,4]
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
            ]),
        AllowedLanguages::createFromConfigurationArray([0,3,4,5])
        );

        $this->assertEquals($expectedBeGroup, $extendedBeGroup);
    }

    /**
     * @test
     */
    public function can_be_created_only_with_title_and_identifier(): void
    {
        BeGroup::createFromDBValues(['title' => 'some title', 'identifier' => 'some-identifier']);
    }

    /**
     * @test
     */
    public function a_be_group_needs_a_title(): void
    {
        $this->expectException(\RuntimeException::class);
        BeGroup::createFromDBValues(['identifier' => 'some-identifier']);
    }

    /**
     * @test
     */
    public function a_be_group_needs_an_identifier(): void
    {
        $this->expectException(\RuntimeException::class);
        BeGroup::createFromDBValues(['title' => 'some title']);
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
        $allowedLanguages = AllowedLanguages::createFromConfigurationArray([0,3,5]);

        return new BeGroup($identifier, '[PERM] Basic permissions', $nonExcludeFields, $explicitAllowDeny, $allowedLanguages);
    }
}
