<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Collection;

use Iterator;
use IteratorAggregate;
use JsonSerializable;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;

/**
 * @implements IteratorAggregate<int, BeGroup>
 */
final class BeGroupCollection implements IteratorAggregate, JsonSerializable
{
    /**
     * @var BeGroup[]
     */
    private array $beGroups = [];

    /**
     * @var BeGroup[]
     */
    private array $beGroupByIdentifier;

    public function add(BeGroup $beGroup): void
    {
        $this->beGroups[] = $beGroup;
        $this->beGroupByIdentifier[(string)$beGroup->identifier()] = $beGroup;
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

    public function getBeGroupByIdentifier(Identifier $identifier): ?BeGroup
    {
        return $this->beGroupByIdentifier[(string)$identifier] ?? null;
    }
}
