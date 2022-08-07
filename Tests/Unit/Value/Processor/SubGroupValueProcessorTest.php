<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Value\Processor;

use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupRepositoryInterface;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\Processor\SubGroupValueProcessor;
use SebastianHofer\BePermissions\Value\SubGroup;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Value\Processor\SubGroupValueProcessor
 */
final class SubGroupValueProcessorTest extends UnitTestCase
{
    /**
     * @test
     */
    public function replaces_be_groups_uids_with_identifier_strings(): void //phpcs:ignore
    {
        $repo = $this->createMock(BeGroupRepositoryInterface::class);
        $processor = new SubGroupValueProcessor($repo);

        $value = '1,3,4,7';
        $parameters = [['1'], ['3'], ['4'], ['7']];
        $identifiers = [
            new BeGroup(new Identifier('id_one'), new BeGroupFieldCollection()),
            new BeGroup(new Identifier('id_three'), new BeGroupFieldCollection()),
            new BeGroup(new Identifier('id_four'), new BeGroupFieldCollection()),
            new BeGroup(new Identifier('id_seven'), new BeGroupFieldCollection())
        ];

        $repo->expects($this->exactly(4))->method('findOneByUid')
            ->withConsecutive(...$parameters)
            ->willReturnOnConsecutiveCalls(...$identifiers);

        $resultValue = $processor->processValuesFromDatabase($value);

        $expectedValue = 'id_one,id_three,id_four,id_seven';

        $this->assertSame($expectedValue, $resultValue);
    }

    /**
     * @test
     */
    public function replaces_identifiers_with_be_group_uids(): void //phpcs:ignore
    {
        $repo = $this->createMock(BeGroupRepositoryInterface::class);
        $processor = new SubGroupValueProcessor($repo);

        $subGroup = SubGroup::createFromYamlConfiguration(['id_one', 'id_three', 'id_four', 'id_seven']);

        // Needs to be in alphabetical order.
        $parameters = [
            [new Identifier('id_four')],
            [new Identifier('id_one')],
            [new Identifier('id_seven')],
            [new Identifier('id_three')],
        ];

        $beGroupUids = [1,3,4,7];

        $repo->expects($this->exactly(4))->method('findUidByIdentifier')
            ->withConsecutive(...$parameters)
            ->willReturnOnConsecutiveCalls(...$beGroupUids);

        $databaseValue = $processor->processValuesForDatabase($subGroup);

        $expectedValue = '1,3,4,7';

        $this->assertSame($expectedValue, $databaseValue);
    }
}
