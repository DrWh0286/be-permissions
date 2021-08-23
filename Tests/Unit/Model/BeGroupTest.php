<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Model;

use Pluswerk\BePermissions\Value\Identifier;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Model\BeGroup;

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
