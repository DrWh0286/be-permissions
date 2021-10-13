<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Collection;

use Pluswerk\BePermissions\Collection\DuplicateBeGroupFieldException;
use Pluswerk\BePermissions\Value\BeGroupFieldInterface;
use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \Pluswerk\BePermissions\Collection\BeGroupFieldCollection
 */
final class BeGroupFieldCollectionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function a_be_group_field_can_be_added(): void
    {
        $beGroupField = $this->getDummyBeGroupField();

        $collection = new BeGroupFieldCollection();

        $collection->add($beGroupField);

        $this->assertSame($beGroupField, $collection->getBeGroupField(0));
    }

    /**
     * @test
     */
    public function a_be_group_field_collection_can_be_empty(): void
    {
        $collection = new BeGroupFieldCollection();
        $this->assertNull($collection->getBeGroupField(0));
    }

    /**
     * @test
     */
    public function a_field_type_can_be_added_just_once(): void
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
    public function the_collection_is_usable_for_iterations(): void
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
        foreach ($collection as $beGroupField) {
            $actual[] = $beGroupField;
        }

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function can_be_immutable_extended_by_another_collection(): void
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

        $fieldAA->expects($this->once())->method('extend')->with($fieldBA)->willReturn($fieldCA);
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

    private function getMockBeGroupField(string $className)
    {
        return $this->getMockBuilder(BeGroupFieldInterface::class)
            ->setMockClassName($className)
            ->getMock();
    }

    private function getDummyBeGroupField(): BeGroupFieldInterface
    {
        return new class implements BeGroupFieldInterface {
            public static function createFromDBValue(string $dbValue): BeGroupFieldInterface
            {
                return new self();
            }

            public static function createFromConfigurationArray(array $confArray): BeGroupFieldInterface
            {
                return new self();
            }

            public function asArray(): array
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
        };
    }
}
