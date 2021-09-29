<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Model;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Model\BeGroup;
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

        $this->assertSame(
            [
                'pages' => ['media', 'hidden'],
                'tt_content' => ['pages', 'date']
            ],
            $group->nonExcludeFields()
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
            'non_exclude_fields' => 'pages:media,pages:hidden,tt_content:pages,tt_content:date'
        ];

        $expectedGroup = new BeGroup(
            new Identifier('some-identifier'),
            'A title',
            [
                'pages' => ['media', 'hidden'],
                'tt_content' => ['pages', 'date']
            ]
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
                'non_exclude_fields' => 'pages:media,pages:hidden,tt_content:pages,tt_content:date'
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
            ]
        ];
        $configuration = new BeGroupConfiguration($group->identifier(), '', $config);

        $overruledBeGroup = $group->overruleByConfiguration($configuration);

        $expectedBeGroup = new BeGroup($group->identifier(), 'Some new group title', [
            'pages' => [
                'title'
            ],
            'tt_content' => [
                'some_additiona_field',
                'another_field',
                'hidden'
            ]
        ]);

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
            ]
        ];
        $configuration = new BeGroupConfiguration($group->identifier(), '', $config);

        $extendedBeGroup = $group->extendByConfiguration($configuration);

        $expectedBeGroup = new BeGroup($group->identifier(), $group->title(), [
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
        ]);

        $this->assertEquals($expectedBeGroup, $extendedBeGroup);
    }

    private function createTestGroup(): BeGroup
    {
        $identifier = new Identifier('some-identifier');
        $nonExcludeFields = [
            'pages' => ['media', 'hidden'],
            'tt_content' => ['pages', 'date']
        ];

        return new BeGroup($identifier, '[PERM] Basic permissions', $nonExcludeFields);
    }
}
