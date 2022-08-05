<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Collection;

use Iterator;
use IteratorAggregate;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Model\BeGroup;

/**
 * @implements IteratorAggregate<BeGroupOverviewItem>
 */
final class BeGroupOverviewCollection implements IteratorAggregate
{
    /**
     * @var BeGroupOverviewItem[]
     */
    private array $itemsByIdentifier;

    /**
     * @var BeGroupOverviewItem[]
     */
    private array $items;

    public function __construct(BeGroupCollection $beGroupRecords, BeGroupConfigurationCollection $beGroupsConfigurations)
    {
        /** @var BeGroup $beGroupRecord */
        foreach ($beGroupRecords as $beGroupRecord) {
            if (!isset($this->itemsByIdentifier[(string)$beGroupRecord->identifier()])) {
                $this->itemsByIdentifier[(string)$beGroupRecord->identifier()] = new BeGroupOverviewItem($beGroupRecord->identifier());
            }

            $this->itemsByIdentifier[(string)$beGroupRecord->identifier()]->addBeGroupRecord($beGroupRecord);
        }

        /** @var BeGroupConfiguration $beGroupsConfiguration */
        foreach ($beGroupsConfigurations as $beGroupsConfiguration) {
            if (!isset($this->itemsByIdentifier[(string)$beGroupsConfiguration->identifier()])) {
                $this->itemsByIdentifier[(string)$beGroupsConfiguration->identifier()] = new BeGroupOverviewItem($beGroupsConfiguration->identifier());
            }

            $this->itemsByIdentifier[(string)$beGroupsConfiguration->identifier()]->addBeGroupConfiguration($beGroupsConfiguration);
        }

        foreach ($this->itemsByIdentifier as $item) {
            $this->items[] = $item;
        }
    }

    public function getItem(int $position): ?BeGroupOverviewItem
    {
        return $this->items[$position] ?? null;
    }

    public function getIterator(): Iterator
    {
        return new class ($this) implements Iterator {
            private BeGroupOverviewCollection $beGroupOverviewCollection;
            private int $position = 0;

            public function __construct(BeGroupOverviewCollection $beGroupOverviewCollection)
            {
                $this->beGroupOverviewCollection = $beGroupOverviewCollection;
            }

            public function current(): ?BeGroupOverviewItem
            {
                return $this->beGroupOverviewCollection->getItem($this->position);
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
                return $this->beGroupOverviewCollection->getItem($this->position) instanceof BeGroupOverviewItem;
            }

            public function rewind(): void
            {
                $this->position = 0;
            }
        };
    }
}
