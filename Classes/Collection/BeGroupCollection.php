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

namespace SebastianHofer\BePermissions\Collection;

use Iterator;
use IteratorAggregate;
use JsonSerializable;
use SebastianHofer\BePermissions\Model\BeGroup;

/**
 * @implements IteratorAggregate<int, BeGroup>
 */
final class BeGroupCollection implements IteratorAggregate, JsonSerializable
{
    /**
     * @var BeGroup[]
     */
    private array $beGroups = [];

    public function add(BeGroup $beGroup): void
    {
        $this->beGroups[] = $beGroup;
    }

    public function getBeGroup(int $position): ?BeGroup
    {
        return $this->beGroups[$position] ?? null;
    }

    public function isEmpty(): bool
    {
        return (count($this->beGroups) === 0);
    }

    public function getIterator(): Iterator
    {
        return new class ($this) implements Iterator {
            private BeGroupCollection $beGroupFieldCollection;
            private int $position = 0;

            public function __construct(BeGroupCollection $beGroupFieldCollection)
            {
                $this->beGroupFieldCollection = $beGroupFieldCollection;
            }

            public function current(): ?BeGroup
            {
                return $this->beGroupFieldCollection->getBeGroup($this->position);
            }

            public function next(): void
            {
                $this->position++;
            }

            public function key(): int
            {
                return $this->position;
            }

            public function valid(): bool
            {
                return $this->beGroupFieldCollection->getBeGroup($this->position) instanceof BeGroup;
            }

            public function rewind(): void
            {
                $this->position = 0;
            }
        };
    }

    /**
     * @return BeGroup[]
     */
    public function jsonSerialize(): array
    {
        return $this->beGroups;
    }
}
