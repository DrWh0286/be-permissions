<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "form_consent".
 *
 * Copyright (C) 2022 Sebastian Hofer <sebastian.hofer@s-hofer.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

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
}
