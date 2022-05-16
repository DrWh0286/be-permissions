<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Collection;

use Iterator;
use IteratorAggregate;
use JsonSerializable;
use Pluswerk\BePermissions\Model\BeGroup;

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
