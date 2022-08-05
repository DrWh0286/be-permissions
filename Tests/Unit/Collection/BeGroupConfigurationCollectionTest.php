<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Unit\Collection;

use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Collection\BeGroupConfigurationCollection;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Value\Identifier;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * @covers \SebastianHofer\BePermissions\Collection\BeGroupConfigurationCollection
 */
final class BeGroupConfigurationCollectionTest extends UnitTestCase
{
    /**
     * @test
     */
    public function a_be_group_configuration_can_be_added(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_group');
        $beGroupFieldCollection = new BeGroupFieldCollection();
        $conf = new BeGroupConfiguration($identifier, '/config/path', $beGroupFieldCollection);

        $configCollection = new BeGroupConfigurationCollection();

        $configCollection->add($conf);

        $this->assertSame($conf, $configCollection->getBeGroupConfiguration(0));
    }

    /**
     * @test
     */
    public function can_be_empty(): void // phpcs:ignore
    {
        $configCollection = new BeGroupConfigurationCollection();
        $this->assertNull($configCollection->getBeGroupConfiguration(0));
        $this->assertTrue($configCollection->isEmpty());
    }

    /**
     * @test
     */
    public function is_iterable(): void // phpcs:ignore
    {
        $identifierA = new Identifier('some_group_a');
        $beGroupFieldCollectionA = new BeGroupFieldCollection();
        $confA = new BeGroupConfiguration($identifierA, '/config/path', $beGroupFieldCollectionA);
        $identifierB = new Identifier('some_group_b');
        $beGroupFieldCollectionB = new BeGroupFieldCollection();
        $confB = new BeGroupConfiguration($identifierB, '/config/path', $beGroupFieldCollectionB);
        $identifierC = new Identifier('some_group_c');
        $beGroupFieldCollectionC = new BeGroupFieldCollection();
        $confC = new BeGroupConfiguration($identifierC, '/config/path', $beGroupFieldCollectionC);

        $configCollection = new BeGroupConfigurationCollection();

        $configCollection->add($confA);
        $configCollection->add($confB);
        $configCollection->add($confC);

        $expectedIdentifiers = [
            'some_group_a',
            'some_group_b',
            'some_group_c',
        ];
        $actualIdentifiers = [];

        /** @var BeGroupConfiguration $beGroupConfiguration */
        foreach ($configCollection as $beGroupConfiguration) {
            $actualIdentifiers[] = (string)$beGroupConfiguration->identifier();
        }

        $this->assertSame($expectedIdentifiers, $actualIdentifiers);
    }

    /**
     * @test
     */
    public function configuration_can_be_fetched_by_identifier(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_fetch_identifier');
        $beGroupFieldCollection = new BeGroupFieldCollection();
        $conf = new BeGroupConfiguration($identifier, '/config/path', $beGroupFieldCollection);

        $configCollection = new BeGroupConfigurationCollection();

        $configCollection->add($conf);

        $this->assertSame($conf, $configCollection->getBeGroupConfigurationByIdentifier($identifier));
    }

    /**
     * @test
     */
    public function if_no_configuration_exists_for_given_identifier_null_is_returned(): void // phpcs:ignore
    {
        $identifier = new Identifier('some_fetch_identifier');
        $beGroupFieldCollection = new BeGroupFieldCollection();
        $conf = new BeGroupConfiguration($identifier, '/config/path', $beGroupFieldCollection);

        $configCollection = new BeGroupConfigurationCollection();

        $configCollection->add($conf);

        $otherIdentifier = new Identifier('other_identifier');

        $this->assertNull($configCollection->getBeGroupConfigurationByIdentifier($otherIdentifier));
    }
}
