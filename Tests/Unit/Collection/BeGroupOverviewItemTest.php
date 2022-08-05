<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Collection;

use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Collection\BeGroupOverviewItem;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Collection\BeGroupOverviewItem
 */
final class BeGroupOverviewItemTest extends UnitTestCase
{
    /**
     * @test
     */
    public function holds_identifier(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $this->assertSame($identifier, $item->getIdentifier());
    }

    /**
     * @test
     */
    public function can_hold_the_be_group_record(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $fieldCol = new BeGroupFieldCollection();
        $beGroup = new BeGroup($identifier, $fieldCol);

        $item->addBeGroupRecord($beGroup);

        $this->assertSame($beGroup, $item->beGroupRecord());
    }

    /**
     * @test
     */
    public function knows_if_be_group_record_exists(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $fieldCol = new BeGroupFieldCollection();
        $beGroup = new BeGroup($identifier, $fieldCol);

        $item->addBeGroupRecord($beGroup);

        $this->assertTrue($item->beGroupRecordExists());
    }

    /**
     * @test
     */
    public function knows_if_be_group_record_does_not_exist(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $this->assertFalse($item->beGroupRecordExists());
    }
    /**
     * @test
     */
    public function can_hold_the_be_group_configuration(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $fieldCol = new BeGroupFieldCollection();
        $beGroupConfiguration = new BeGroupConfiguration($identifier, '/path', $fieldCol);

        $item->addBeGroupConfiguration($beGroupConfiguration);

        $this->assertSame($beGroupConfiguration, $item->beGroupConfiguration());
    }

    /**
     * @test
     */
    public function knows_if_be_group_configiuration_exists(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $fieldCol = new BeGroupFieldCollection();
        $beGroupConfiguration = new BeGroupConfiguration($identifier, '/path', $fieldCol);

        $item->addBeGroupConfiguration($beGroupConfiguration);

        $this->assertTrue($item->beGroupConfigurationExists());
    }

    /**
     * @test
     */
    public function knows_if_be_group_configuration_does_not_exist(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $this->assertFalse($item->beGroupConfigurationExists());
    }

    /**
     * @test
     */
    public function item_can_only_be_filled_with_record_with_correct_identifier(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $fieldCol = new BeGroupFieldCollection();
        $otherIdentifier = new Identifier('other_identifier');
        $beGroup = new BeGroup($otherIdentifier, $fieldCol);

        $this->expectException(\InvalidArgumentException::class);
        $item->addBeGroupRecord($beGroup);
    }

    /**
     * @test
     */
    public function item_can_only_be_filled_with_configuration_with_correct_identifier(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $fieldCol = new BeGroupFieldCollection();
        $otherIdentifier = new Identifier('other_identifier');
        $beGroupConfiguration = new BeGroupConfiguration($otherIdentifier, '/path', $fieldCol);

        $this->expectException(\InvalidArgumentException::class);
        $item->addBeGroupConfiguration($beGroupConfiguration);
    }

    /**
     * @test
     */
    public function knows_if_record_and_configuration_are_in_synch(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_identifier');
        $item = new BeGroupOverviewItem($identifier);

        $fieldColBeGroup = new BeGroupFieldCollection();
        $beGroup = new BeGroup($identifier, $fieldColBeGroup);

        $fieldColConfig = new BeGroupFieldCollection();
        $beGroupConfiguration = new BeGroupConfiguration($identifier, '/path', $fieldColConfig);

        $item->addBeGroupRecord($beGroup);
        $item->addBeGroupConfiguration($beGroupConfiguration);

        $this->assertTrue($item->getInSynch());
    }
}
