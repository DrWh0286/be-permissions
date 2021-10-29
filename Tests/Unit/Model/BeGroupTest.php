<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Model;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
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
    public function a_group_has_a_title(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $this->assertSame('[PERM] Basic permissions', $group->title());
    }

    /**
     * @test
     */
    public function a_group_has_an_identifier(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $expectedIdentifier = new Identifier('some-identifier');

        $this->assertEquals($expectedIdentifier, $group->identifier());
    }

    /**
     * @test
     */
    public function a_be_group_has_be_group_field_collection(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration([
            'pages' => ['media', 'hidden'],
            'tt_content' => ['pages', 'date']
        ]);

        $explicitAllowDeny = ExplicitAllowDeny::createFromYamlConfiguration(
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

        $expectedCollection = new BeGroupFieldCollection();
        $expectedCollection->add($nonExcludeFields);
        $expectedCollection->add($explicitAllowDeny);
        $expectedCollection->add($allowedLanguages);

        $this->assertEquals($expectedCollection, $group->beGroupFieldCollection());
    }

    /**
     * @test
     */
    public function can_be_created_from_database_values(): void //phpcs:ignore
    {
        $dbValues = [
            'identifier' => 'some-identifier',
            'title' => 'A title',
            'non_exclude_fields' => 'pages:media,pages:hidden,tt_content:pages,tt_content:date',
            'explicit_allowdeny' => 'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:some_plugina:ALLOW,tt_content:list_type:another_pluginb:ALLOW',
            'allowed_languages' => '0,3,5'
        ];

        $collection = new BeGroupFieldCollection();

        $collection->add(NonExcludeFields::createFromYamlConfiguration([
            'pages' => ['media', 'hidden'],
            'tt_content' => ['pages', 'date']
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

        $expectedGroup = new BeGroup(new Identifier('some-identifier'), 'A title', $collection);

        $this->assertEquals($expectedGroup, BeGroup::createFromDBValues($dbValues));
    }

    /**
     * @test
     */
    public function can_be_prepared_for_writing_to_database(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $this->assertSame(
            [
                'identifier' => 'some-identifier',
                'non_exclude_fields' => 'pages:media,pages:hidden,tt_content:pages,tt_content:date',
                'explicit_allowdeny' => 'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:some_plugina:ALLOW,tt_content:list_type:another_pluginb:ALLOW',
                'allowed_languages' => '0,3,5',
                'title' => '[PERM] Basic permissions',
            ],
            $group->databaseValues()
        );
    }

    /**
     * @test
     */
    public function can_be_overruled_by_be_group_configuration(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $collection = new BeGroupFieldCollection();
        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]
        ));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration(
            [
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
        ));
        $collection->add(AllowedLanguages::createFromYamlConfiguration([1,2,4]));

        $configuration = new BeGroupConfiguration($group->identifier(), '', 'Some new group title', $collection);

        $overruledBeGroup = $group->overruleByConfiguration($configuration);

        $collection = new BeGroupFieldCollection();

        $collection->add(NonExcludeFields::createFromYamlConfiguration([
            'pages' => [
                'title'
            ],
            'tt_content' => [
                'some_additiona_field',
                'another_field',
                'hidden'
            ]
        ]));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration([
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
        $collection->add(AllowedLanguages::createFromYamlConfiguration([1,2,4]));

        $expectedBeGroup = new BeGroup($group->identifier(), 'Some new group title', $collection);

        $this->assertEquals($expectedBeGroup, $overruledBeGroup);
    }

    /**
     * @test
     */
    public function can_be_extended_by_configuration(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $collection = new BeGroupFieldCollection();

        $collection->add(NonExcludeFields::createFromYamlConfiguration([
            'pages' => [
                'title'
            ],
            'tt_content' => [
                'some_additiona_field',
                'another_field',
                'hidden'
            ]
        ]));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration([
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
        ]));
        $collection->add(AllowedLanguages::createFromYamlConfiguration([3,4]));

        $configuration = new BeGroupConfiguration($group->identifier(), '', 'Some new group title', $collection);

        $extendedBeGroup = $group->extendByConfiguration($configuration);

        $collection = new BeGroupFieldCollection();

        $collection->add(NonExcludeFields::createFromYamlConfiguration([
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
        ]));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration([
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
        $collection->add(AllowedLanguages::createFromYamlConfiguration([0,3,4,5]));

        $expectedBeGroup = new BeGroup($group->identifier(), $group->title(), $collection);

        $this->assertEquals($expectedBeGroup, $extendedBeGroup);
    }

    /**
     * @test
     */
    public function can_be_created_only_with_title_and_identifier(): void //phpcs:ignore
    {
        BeGroup::createFromDBValues(['title' => 'some title', 'identifier' => 'some-identifier']);
    }

    /**
     * @test
     */
    public function a_be_group_needs_a_title(): void //phpcs:ignore
    {
        $this->expectException(\RuntimeException::class);
        BeGroup::createFromDBValues(['identifier' => 'some-identifier']);
    }

    /**
     * @test
     */
    public function a_be_group_needs_an_identifier(): void //phpcs:ignore
    {
        $this->expectException(\RuntimeException::class);
        BeGroup::createFromDBValues(['title' => 'some title']);
    }

    private function createTestGroup(): BeGroup
    {
        $identifier = new Identifier('some-identifier');
        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration([
            'pages' => ['media', 'hidden'],
            'tt_content' => ['pages', 'date']
        ]);

        $explicitAllowDeny = ExplicitAllowDeny::createFromYamlConfiguration(
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

        $beGroupFieldCollection = new BeGroupFieldCollection();
        $beGroupFieldCollection->add($nonExcludeFields);
        $beGroupFieldCollection->add($explicitAllowDeny);
        $beGroupFieldCollection->add($allowedLanguages);

        return new BeGroup($identifier, '[PERM] Basic permissions', $beGroupFieldCollection);
    }
}
