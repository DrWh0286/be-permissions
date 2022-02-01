<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Hook;

use Pluswerk\BePermissions\Value\Identifier;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use Pluswerk\BePermissions\Hook\DataHandlerBeGroupsIdentifierHook;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Hook\DataHandlerBeGroupsIdentifierHook
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
}
