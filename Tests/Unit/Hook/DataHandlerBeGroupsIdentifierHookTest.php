<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Hook;

use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use SebastianHofer\BePermissions\Hook\DataHandlerBeGroupsIdentifierHook;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Hook\DataHandlerBeGroupsIdentifierHook
 */
final class DataHandlerBeGroupsIdentifierHookTest extends UnitTestCase
{
    /**
     * @test
     */
    public function if_identifier_is_empty_it_is_created_and_set_from_group_title(): void //phpcs:ignore
    {
        $title = 'group title';
        $incomingFieldArray = [
            'title' => $title,
            'identifier' => ''
        ];
        $table = 'be_groups';
        $id = 'NEW' . md5('this is a new id');
        $dataHandler = $this->createMock(DataHandler::class);

        $hook = new DataHandlerBeGroupsIdentifierHook();

        $hook->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $dataHandler);

        $this->assertSame(
            (string)Identifier::buildNewFromTitle($title),
            $incomingFieldArray['identifier']
        );
    }

    /**
     * @test
     */
    public function if_table_is_not_be_groups_nothing_happens(): void //phpcs:ignore
    {
        $title = 'group title';
        $incomingFieldArray = [
            'title' => $title,
            'identifier' => ''
        ];
        $table = 'other_table';
        $id = 'NEW' . md5('this is a new id');
        $dataHandler = $this->createMock(DataHandler::class);

        $hook = new DataHandlerBeGroupsIdentifierHook();

        $hook->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $dataHandler);

        $this->assertSame('', $incomingFieldArray['identifier']);
    }

    /**
     * @test
     */
    public function if_title_is_no_string_a_md5_hash_is_set(): void //phpcs:ignore
    {
        $title = ['group title'];
        $incomingFieldArray = [
            'title' => $title,
            'identifier' => ''
        ];
        $table = 'be_groups';
        $id = 'NEW' . md5('this is a new id');
        $dataHandler = $this->createMock(DataHandler::class);

        $hook = new DataHandlerBeGroupsIdentifierHook();

        $hook->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $dataHandler);

        $this->assertNotEmpty($incomingFieldArray['identifier']);
    }

    /**
     * @test
     */
    public function if_title_is_not_set_in_incoming_field_array_identifier_is_not_updated(): void //phpcs:ignore
    {
        $incomingFieldArray = [
            'identifier' => '',
            'anotherField' => 123
        ];
        $table = 'be_groups';
        $id = 'NEW' . md5('this is a new id');
        $dataHandler = $this->createMock(DataHandler::class);

        $hook = new DataHandlerBeGroupsIdentifierHook();

        $hook->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $dataHandler);

        $this->assertEmpty($incomingFieldArray['identifier']);
    }

    /**
     * @test
     */
    public function if_identifier_is_not_set_in_incoming_field_array_identifier_is_not_updated(): void //phpcs:ignore
    {
        $incomingFieldArray = [
            'title' => 'Some Title'
        ];
        $table = 'be_groups';
        $id = 'NEW' . md5('this is a new id');
        $dataHandler = $this->createMock(DataHandler::class);

        $hook = new DataHandlerBeGroupsIdentifierHook();

        $hook->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $dataHandler);

        $this->assertNull($incomingFieldArray['identifier'] ?? null);
    }

    /**
     * @test
     */
    public function if_identifier_is_already_set_it_is_not_updated(): void //phpcs:ignore
    {
        $incomingFieldArray = [
            'identifier' => 'already_set_identifier',
            'title' => 'Some title'
        ];
        $table = 'be_groups';
        $id = 'NEW' . md5('this is a new id');
        $dataHandler = $this->createMock(DataHandler::class);

        $hook = new DataHandlerBeGroupsIdentifierHook();

        $hook->processDatamap_preProcessFieldArray($incomingFieldArray, $table, $id, $dataHandler);

        $this->assertSame('already_set_identifier', $incomingFieldArray['identifier']);
    }
}
