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

namespace SebastianHofer\BePermissions\Tests\Unit\Model;

use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilderInterface;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Value\AllowedLanguages;
use SebastianHofer\BePermissions\Value\CodeManagedGroup;
use SebastianHofer\BePermissions\Value\DeployProcessing;
use SebastianHofer\BePermissions\Value\ExplicitAllowDeny;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\NonExcludeFields;
use SebastianHofer\BePermissions\Value\SubGroup;
use SebastianHofer\BePermissions\Value\Title;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Model\BeGroup
 * @uses \SebastianHofer\BePermissions\Configuration\BeGroupConfiguration
 * @uses \SebastianHofer\BePermissions\Value\AllowedLanguages
 * @uses \SebastianHofer\BePermissions\Value\ExplicitAllowDeny
 * @uses \SebastianHofer\BePermissions\Value\Identifier
 * @uses \SebastianHofer\BePermissions\Value\NonExcludeFields
 */
final class BeGroupTest extends UnitTestCase
{
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

        $title = Title::createFromYamlConfiguration('[PERM] Basic permissions');
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
        $expectedCollection->add($title);
        $expectedCollection->add($nonExcludeFields);
        $expectedCollection->add($explicitAllowDeny);
        $expectedCollection->add($allowedLanguages);

        $this->assertEquals($expectedCollection, $group->beGroupFieldCollection());
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
                'title' => '[PERM] Basic permissions',
                'non_exclude_fields' => 'pages:hidden,pages:media,tt_content:date,tt_content:pages',
                'explicit_allowdeny' => 'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:another_pluginb:ALLOW,tt_content:list_type:some_plugina:ALLOW',
                'allowed_languages' => '0,3,5'
            ],
            $group->databaseValues()
        );

        $this->assertSame(
            [
                'identifier' => 'some-identifier',
                'title' => '[PERM] Basic permissions',
                'non_exclude_fields' => 'pages:hidden,pages:media,tt_content:date,tt_content:pages',
                'explicit_allowdeny' => 'tt_content:CType:header:ALLOW,tt_content:CType:text:ALLOW,tt_content:CType:textpic:ALLOW,tt_content:list_type:another_pluginb:ALLOW,tt_content:list_type:some_plugina:ALLOW',
                'allowed_languages' => '0,3,5'
            ],
            $group->getDatabaseValues()
        );
    }

    /**
     * @test
     */
    public function can_be_overruled_by_be_group_configuration(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $collection = new BeGroupFieldCollection();
        $collection->add(Title::createFromYamlConfiguration('Some new group title'));
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

        $configuration = new BeGroupConfiguration($group->identifier(), '', $collection);

        $overruledBeGroup = $group->overruleByConfiguration($configuration);

        $collection = new BeGroupFieldCollection();

        $collection->add(Title::createFromYamlConfiguration('Some new group title'));
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

        $expectedBeGroup = new BeGroup($group->identifier(), $collection);

        $this->assertEquals($expectedBeGroup, $overruledBeGroup);
    }

    /**
     * @test
     */
    public function can_be_extended_by_configuration(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $collection = new BeGroupFieldCollection();

        $collection->add(Title::createFromYamlConfiguration('Some new group title'));
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

        $configuration = new BeGroupConfiguration($group->identifier(), '', $collection);

        $extendedBeGroup = $group->extendByConfiguration($configuration);

        $collection = new BeGroupFieldCollection();

        $collection->add(Title::createFromYamlConfiguration('[PERM] Basic permissions'));

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

        $expectedBeGroup = new BeGroup($group->identifier(), $collection);

        $this->assertEquals($expectedBeGroup, $extendedBeGroup);
    }

    /**
     * @test
     */
    public function be_group_is_json_serializable(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $this->assertSame(json_encode([
            'identifier' => 'some-identifier',
            'beGroupFieldCollection' => [
                'title' => '[PERM] Basic permissions',
                'non_exclude_fields' => [
                    'pages' => ['hidden', 'media'],
                    'tt_content' => ['date', 'pages']
                ],
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
                'allowed_languages' => [0,3,5]
            ]
        ]), json_encode($group));
    }

    /**
     * @test
     */
    public function can_be_created_from_json(): void //phpcs:ignore
    {
        $jsonString = json_encode([
            'identifier' => 'some-identifier',
            'beGroupFieldCollection' => [
                'title' => '[PERM] Basic permissions',
                'non_exclude_fields' => [
                    'pages' => ['hidden', 'media'],
                    'tt_content' => ['date', 'pages']
                ],
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
                'allowed_languages' => [0,3,5]
            ]
        ]);

        $expectedBeGroup = $this->createTestGroup();

        $builder = $this->createMock(BeGroupFieldCollectionBuilderInterface::class);
        $collection = $expectedBeGroup->beGroupFieldCollection();
        $builder->expects($this->once())->method('buildFromConfigurationArray')->willReturn($collection);
        $beGroup = BeGroup::createFromJson((string)$jsonString, $builder);

        $this->assertEquals($expectedBeGroup, $beGroup);
    }

    /**
     * @test
     */
    public function is_detected_as_code_managed(): void //phpcs:ignore
    {
        $group = $this->createCodeManagedTestGroup();

        $this->assertTrue($group->isCodeManaged());
    }

    /**
     * @test
     */
    public function is_not_detected_as_code_managed(): void //phpcs:ignore
    {
        $group = $this->createNotCodeManagedTestGroup();

        $this->assertFalse($group->isCodeManaged());
    }

    /**
     * @test
     */
    public function is_not_detected_as_code_managed_with_undefined_state(): void //phpcs:ignore
    {
        $group = $this->createTestGroup();

        $this->assertFalse($group->isCodeManaged());
    }

    /**
     * @test
     */
    public function knows_its_deploy_processing_overrule(): void //phpcs:ignore
    {
        $fieldCollection = new BeGroupFieldCollection();

        $deployProcessing = DeployProcessing::createOverrule();
        $fieldCollection->add($deployProcessing);

        $group = new BeGroup(new Identifier('tmp'), $fieldCollection);

        $this->assertTrue($group->deployProcessingIsOverrule());
        $this->assertFalse($group->deployProcessingIsExtend());
    }

    /**
     * @test
     */
    public function knows_its_deploy_processing_extend(): void //phpcs:ignore
    {
        $fieldCollection = new BeGroupFieldCollection();

        $deployProcessing = DeployProcessing::createExtend();
        $fieldCollection->add($deployProcessing);

        $group = new BeGroup(new Identifier('tmp'), $fieldCollection);

        $this->assertTrue($group->deployProcessingIsExtend());
        $this->assertFalse($group->deployProcessingIsOverrule());
    }

    /**
     * @test
     */
    public function fall_back_is_false_for_deploy_processing(): void //phpcs:ignore
    {
        $fieldCollection = new BeGroupFieldCollection();

        $group = new BeGroup(new Identifier('tmp'), $fieldCollection);

        $this->assertFalse($group->deployProcessingIsOverrule());
        $this->assertFalse($group->deployProcessingIsExtend());
    }

    /**
     * @test
     */
    public function get_subgroup_field(): void //phpcs:ignore
    {
        $title = Title::createFromYamlConfiguration('new group with subgroups');
        $identifier = Identifier::buildNewFromTitle((string)$title);
        $subGroupField = SubGroup::createFromYamlConfiguration(['test-group-c', 'test-group-d']);
        $fieldCollection = new BeGroupFieldCollection();
        $fieldCollection->add($subGroupField);
        $fieldCollection->add($title);

        $group = new BeGroup($identifier, $fieldCollection);

        $this->assertSame($subGroupField, $group->getSubGroup());
    }

    private function createTestGroup(): BeGroup
    {
        $identifier = new Identifier('some-identifier');
        $title = Title::createFromYamlConfiguration('[PERM] Basic permissions');
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
        $beGroupFieldCollection->add($title);
        $beGroupFieldCollection->add($nonExcludeFields);
        $beGroupFieldCollection->add($explicitAllowDeny);
        $beGroupFieldCollection->add($allowedLanguages);

        return new BeGroup($identifier, $beGroupFieldCollection);
    }

    private function createCodeManagedTestGroup(): BeGroup
    {
        $identifier = new Identifier('some-identifier');
        $title = Title::createFromYamlConfiguration('[PERM] Basic permissions');
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
        $codeManaged = CodeManagedGroup::createFromDBValue('1');

        $beGroupFieldCollection = new BeGroupFieldCollection();
        $beGroupFieldCollection->add($title);
        $beGroupFieldCollection->add($nonExcludeFields);
        $beGroupFieldCollection->add($explicitAllowDeny);
        $beGroupFieldCollection->add($allowedLanguages);
        $beGroupFieldCollection->add($codeManaged);

        return new BeGroup($identifier, $beGroupFieldCollection);
    }

    private function createNotCodeManagedTestGroup(): BeGroup
    {
        $identifier = new Identifier('some-identifier');
        $title = Title::createFromYamlConfiguration('[PERM] Basic permissions');
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
        $codeManaged = CodeManagedGroup::createFromDBValue('0');

        $beGroupFieldCollection = new BeGroupFieldCollection();
        $beGroupFieldCollection->add($title);
        $beGroupFieldCollection->add($nonExcludeFields);
        $beGroupFieldCollection->add($explicitAllowDeny);
        $beGroupFieldCollection->add($allowedLanguages);
        $beGroupFieldCollection->add($codeManaged);

        return new BeGroup($identifier, $beGroupFieldCollection);
    }
}
