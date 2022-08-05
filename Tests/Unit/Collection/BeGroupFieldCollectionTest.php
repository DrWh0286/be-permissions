<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Collection;

use PHPUnit\Framework\MockObject\MockObject;
use SebastianHofer\BePermissions\Collection\DuplicateBeGroupFieldException;
use SebastianHofer\BePermissions\Value\BeGroupFieldInterface;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Value\Title;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Collection\BeGroupFieldCollection
 */
final class BeGroupFieldCollectionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function a_be_group_field_can_be_added(): void //phpcs:ignore
    {
        $beGroupField = $this->getDummyBeGroupField();

        $collection = new BeGroupFieldCollection();

        $collection->add($beGroupField);

        $this->assertSame($beGroupField, $collection->getBeGroupField(0));
    }

    /**
     * @test
     */
    public function a_be_group_field_collection_can_be_empty(): void //phpcs:ignore
    {
        $collection = new BeGroupFieldCollection();
        $this->assertNull($collection->getBeGroupField(0));
    }

    /**
     * @test
     */
    public function a_field_type_can_be_added_just_once(): void //phpcs:ignore
    {
        $beGroupFieldA = $this->getMockBuilder(BeGroupFieldInterface::class)
            ->setMockClassName('SomeBeGroupFieldImplementation')
            ->getMock();
        $beGroupFieldB = $this->getMockBuilder(BeGroupFieldInterface::class)
            ->setMockClassName('SomeBeGroupFieldImplementation')
            ->getMock();

        $collection = new BeGroupFieldCollection();
        $collection->add($beGroupFieldA);

        $this->expectException(DuplicateBeGroupFieldException::class);
        $collection->add($beGroupFieldB);
    }

    /**
     * @test
     */
    public function the_collection_is_usable_for_iterations(): void //phpcs:ignore
    {
        $beGroupFieldA = $this->getMockBeGroupField('SomeBeGroupFieldA');
        $beGroupFieldB = $this->getMockBeGroupField('SomeBeGroupFieldB');
        $beGroupFieldC = $this->getMockBeGroupField('SomeBeGroupFieldC');

        $expected = [$beGroupFieldA, $beGroupFieldB, $beGroupFieldC];

        $collection = new BeGroupFieldCollection();

        $collection->add($beGroupFieldA);
        $collection->add($beGroupFieldB);
        $collection->add($beGroupFieldC);

        $actual = [];
        $i = 0;
        foreach ($collection as $key => $beGroupField) {
            $actual[] = $beGroupField;
            $this->assertSame($i++, $key);
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function can_be_immutable_extended_by_another_collection(): void //phpcs:ignore
    {
        $fieldAA = $this->getMockBeGroupField('FieldA');
        $fieldAB = $this->getMockBeGroupField('FieldB');
        $fieldAC = $this->getMockBeGroupField('FieldC');
        $fieldBA = $this->getMockBeGroupField('FieldA');
        $fieldBB = $this->getMockBeGroupField('FieldB');
        $fieldBD = $this->getMockBeGroupField('FieldD');

        $fieldCA = $this->getMockBeGroupField('FieldCA');
        $fieldCB = $this->getMockBeGroupField('FieldCB');

        $collectionA = new BeGroupFieldCollection();
        $collectionB = new BeGroupFieldCollection();

        $collectionA->add($fieldAA);
        $collectionA->add($fieldAB);
        $collectionA->add($fieldAC);

        $collectionB->add($fieldBA);
        $collectionB->add($fieldBB);
        $collectionB->add($fieldBD);

        /** @var MockObject $fieldAA */
        $fieldAA->expects($this->once())->method('extend')->with($fieldBA)->willReturn($fieldCA);
        /** @var MockObject $fieldAB */
        $fieldAB->expects($this->once())->method('extend')->with($fieldBB)->willReturn($fieldCB);

        $resultingCollection = $collectionA->extend($collectionB);

        $expectedCollection = new BeGroupFieldCollection();
        $expectedCollection->add($fieldCA);
        $expectedCollection->add($fieldCB);
        $expectedCollection->add($fieldBD);
        $expectedCollection->add($fieldAC);

        $this->assertEquals($expectedCollection, $resultingCollection);

        $this->assertNotSame($resultingCollection, $collectionA);
        $this->assertNotSame($resultingCollection, $collectionB);
    }

    /**
     * @param string $className
     * @return BeGroupFieldInterface
     */
    private function getMockBeGroupField(string $className)
    {
        return $this->getMockBuilder(BeGroupFieldInterface::class)
            ->setMockClassName($className)
            ->getMock();
    }

    /**
     * @test
     * @dataProvider compareProvider
     */
    public function is_comparable_and_false(BeGroupFieldCollection $colBase, BeGroupFieldCollection $colCompare, bool $expected): void // phpcs:ignore
    {
        if ($expected) {
            $this->assertTrue($colBase->isEqual($colCompare));
        } else {
            $this->assertFalse($colBase->isEqual($colCompare));
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     * @throws DuplicateBeGroupFieldException
     */
    public function compareProvider(): array
    {
        $colBaseOne = new BeGroupFieldCollection();
        $fieldBaseA = new Title('Some title A');
        $colBaseOne->add($fieldBaseA);

        $colCompareOne = new BeGroupFieldCollection();

        $colCompareTwo = new BeGroupFieldCollection();
        $fieldCompareB = new Title('Some title B');
        $colCompareTwo->add($fieldCompareB);

        $colBaseTwo = new BeGroupFieldCollection();

        $colCompareThree = new BeGroupFieldCollection();
        $fieldCompareA = new Title('Some title A');
        $colCompareThree->add($fieldCompareA);

        return [
            'some field is missing in compare col' => [
                'colBase' => $colBaseOne,
                'colCompare' => $colCompareOne,
                'expected' => false
            ],
            'some field is missing in base col' => [
                'colBase' => $colBaseTwo,
                'colCompare' => $colCompareTwo,
                'expected' => false
            ],
            'some field has different content' => [
                'colBase' => $colBaseOne,
                'colCompare' => $colCompareTwo,
                'expected' => false
            ],
            'both are equal' => [
                'colBase' => $colBaseOne,
                'colCompare' => $colCompareThree,
                'expected' => true
            ]
        ];
    }

    private function getDummyBeGroupField(): BeGroupFieldInterface
    {
        return new class implements BeGroupFieldInterface {
            public static function createFromDBValue(string $dbValue): BeGroupFieldInterface
            {
                return new self();
            }

            /** @return string[] */
            public function yamlConfigurationValue(): array
            {
                return [];
            }

            public function extend(BeGroupFieldInterface $beGroupField): BeGroupFieldInterface
            {
                return new self();
            }

            public function getFieldName(): string
            {
                return '';
            }

            public function __toString(): string
            {
                return '';
            }
        };
    }
}
