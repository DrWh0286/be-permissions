<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Collection;

use Iterator;
use IteratorAggregate;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Value\Identifier;

/**
 * @implements IteratorAggregate<BeGroupConfiguration>
 */
final class BeGroupConfigurationCollection implements IteratorAggregate
{
    /**
     * @var BeGroupConfiguration[]
     */
    private array $configurations = [];
    /**
     * @var BeGroupConfiguration[]
     */
    private array $configurationsByIdentifier;

    public function add(BeGroupConfiguration $conf): void
    {
        $this->configurations[] = $conf;
        $this->configurationsByIdentifier[(string)$conf->identifier()] = $conf;
    }

    public function getBeGroupConfiguration(int $position): ?BeGroupConfiguration
    {
        return $this->configurations[$position] ?? null;
    }

    public function getBeGroupConfigurationByIdentifier(Identifier $identifier): ?BeGroupConfiguration
    {
        return $this->configurationsByIdentifier[(string)$identifier] ?? null;
    }

    public function isEmpty(): bool
    {
        return !(count($this->configurations) > 0);
    }

    public function getIterator(): Iterator
    {
        return new class ($this) implements Iterator {
            private BeGroupConfigurationCollection $beGroupConfigurationCollection;
            private int $position = 0;

            public function __construct(BeGroupConfigurationCollection $beGroupConfigurationCollection)
            {
                $this->beGroupConfigurationCollection = $beGroupConfigurationCollection;
            }

            public function current(): ?BeGroupConfiguration
            {
                return $this->beGroupConfigurationCollection->getBeGroupConfiguration($this->position);
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
                return $this->beGroupConfigurationCollection->getBeGroupConfiguration($this->position) instanceof BeGroupConfiguration;
            }

            public function rewind(): void
            {
                $this->position = 0;
            }
        };
    }
}
