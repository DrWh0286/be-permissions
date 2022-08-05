<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Collection;

use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use SebastianHofer\BePermissions\Collection\BeGroupCollection;

/**
 * @covers \SebastianHofer\BePermissions\Collection\BeGroupCollection
 */
final class BeGroupCollectionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function a_be_group_can_be_added(): void //phpcs:ignore
    {
        $beGroup = new BeGroup(new Identifier('some_identifier'), new BeGroupFieldCollection());

        $col = new BeGroupCollection();

        $col->add($beGroup);

        $this->assertSame($beGroup, $col->getBeGroup(0));
    }

    /**
     * @test
     */
    public function a_be_group_collection_can_be_empty(): void //phpcs:ignore
    {
        $col = new BeGroupCollection();
        $this->assertNull($col->getBeGroup(0));
        $this->assertTrue($col->isEmpty());
    }

    /**
     * @test
     */
    public function the_collection_is_iterable(): void //phpcs:ignore
    {
        $beGroupA = new BeGroup(new Identifier('some_identifier_a'), new BeGroupFieldCollection());
        $beGroupB = new BeGroup(new Identifier('some_identifier_b'), new BeGroupFieldCollection());
        $beGroupC = new BeGroup(new Identifier('some_identifier_c'), new BeGroupFieldCollection());

        $col = new BeGroupCollection();

        $col->add($beGroupA);
        $col->add($beGroupB);
        $col->add($beGroupC);

        $expectedIdentifiers = [
            'some_identifier_a',
            'some_identifier_b',
            'some_identifier_c',
        ];
        $actualIdentifiers = [];

        /** @var BeGroup $beGroup */
        foreach ($col as $beGroup) {
            $actualIdentifiers[] = (string)$beGroup->identifier();
        }

        $this->assertSame($expectedIdentifiers, $actualIdentifiers);
    }

    /**
     * @test
     */
    public function collection_can_be_json_encoded(): void //phpcs:ignore
    {
        $beGroupA = new BeGroup(new Identifier('some_identifier_a'), new BeGroupFieldCollection());
        $beGroupB = new BeGroup(new Identifier('some_identifier_b'), new BeGroupFieldCollection());
        $beGroupC = new BeGroup(new Identifier('some_identifier_c'), new BeGroupFieldCollection());

        $col = new BeGroupCollection();

        $col->add($beGroupA);
        $col->add($beGroupB);
        $col->add($beGroupC);

        $expected = json_encode([$beGroupA, $beGroupB, $beGroupC]);

        $this->assertSame($expected, json_encode($col));
    }

    /**
     * @test
     */
    public function be_group_can_be_fetched_by_identifier(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_fetch_identifier');
        $beGroupFieldCollection = new BeGroupFieldCollection();
        $conf = new BeGroup($identifier, $beGroupFieldCollection);

        $beGroupCollection = new BeGroupCollection();

        $beGroupCollection->add($conf);

        $this->assertSame($conf, $beGroupCollection->getBeGroupByIdentifier($identifier));
    }

    /**
     * @test
     */
    public function if_no_configuration_exists_for_given_identifier_null_is_returned(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_fetch_identifier');
        $beGroupFieldCollection = new BeGroupFieldCollection();
        $conf = new BeGroup($identifier, $beGroupFieldCollection);

        $beGroupCollection = new BeGroupCollection();

        $beGroupCollection->add($conf);

        $otherIdentifier = new Identifier('other_identifier');

        $this->assertNull($beGroupCollection->getBeGroupByIdentifier($otherIdentifier));
    }
}
