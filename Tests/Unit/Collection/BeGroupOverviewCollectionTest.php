<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Collection;

use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Collection\BeGroupConfigurationCollection;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Collection\BeGroupOverviewCollection;
use SebastianHofer\BePermissions\Collection\BeGroupOverviewItem;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Collection\BeGroupOverviewCollection
 */
final class BeGroupOverviewCollectionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function overview_item_is_held_by_collection(): void // phpcs:ignore
    {
        $beGroups = $this->getBeGroups();
        $beGroupConfigs = $this->getBeGroupConfigs();
        $col = new BeGroupOverviewCollection($beGroups, $beGroupConfigs);

        $identifier = new Identifier('identifier_a');
        $expectedItem = new BeGroupOverviewItem($identifier);
        $fieldCol = new BeGroupFieldCollection();
        $groupRec = new BeGroup($identifier, $fieldCol);
        $groupConf = new BeGroupConfiguration($identifier, '/config/path', $fieldCol);

        $expectedItem->addBeGroupRecord($groupRec);
        $expectedItem->addBeGroupConfiguration($groupConf);

        $this->assertEquals($expectedItem, $col->getItem(0));
    }

    /**
     * @test
     */
    public function is_iterable(): void // phpcs:ignore
    {
        $beGroups = $this->getBeGroups();
        $beGroupConfigs = $this->getBeGroupConfigs();
        $col = new BeGroupOverviewCollection($beGroups, $beGroupConfigs);

        $expectedIdentifier = [
            'identifier_a',
            'identifier_b',
            'identifier_c'
        ];
        $actualIdentifier = [];

        /** @var BeGroupOverviewItem $item */
        foreach ($col as $item) {
            $actualIdentifier[] = (string)$item->getIdentifier();
        }

        $this->assertSame($expectedIdentifier, $actualIdentifier);
    }

    private function getBeGroups(): BeGroupCollection
    {
        $col = new BeGroupCollection();
        $groupA = new BeGroup(new Identifier('identifier_a'), new BeGroupFieldCollection());
        $groupB = new BeGroup(new Identifier('identifier_b'), new BeGroupFieldCollection());
        $groupC = new BeGroup(new Identifier('identifier_c'), new BeGroupFieldCollection());

        $col->add($groupA);
        $col->add($groupB);
        $col->add($groupC);

        return $col;
    }

    private function getBeGroupConfigs(): BeGroupConfigurationCollection
    {
        $col = new BeGroupConfigurationCollection();
        $groupA = new BeGroupConfiguration(new Identifier('identifier_a'), '/config/path', new BeGroupFieldCollection());
        $groupB = new BeGroupConfiguration(new Identifier('identifier_b'), '/config/path', new BeGroupFieldCollection());
        $groupC = new BeGroupConfiguration(new Identifier('identifier_c'), '/config/path', new BeGroupFieldCollection());

        $col->add($groupA);
        $col->add($groupB);
        $col->add($groupC);

        return $col;
    }
}
