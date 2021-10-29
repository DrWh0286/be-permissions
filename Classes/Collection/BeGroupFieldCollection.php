<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Collection;

use Iterator;
use IteratorAggregate;
use Pluswerk\BePermissions\Value\BeGroupFieldInterface;

/**
 * @implements IteratorAggregate<BeGroupFieldInterface>
 */
final class BeGroupFieldCollection implements IteratorAggregate
{
    /** @var BeGroupFieldInterface[] $beGroupFields */
    private array $beGroupFields = [];
    /** @var BeGroupFieldInterface[] $associativeBeGroupFields */
    private array $associativeBeGroupFields;

    /**
     * @throws DuplicateBeGroupFieldException
     */
    public function add(BeGroupFieldInterface $beGroupField): void
    {
        $this->beGroupFields[] = $beGroupField;

        if (isset($this->associativeBeGroupFields[get_class($beGroupField)])) {
            throw new DuplicateBeGroupFieldException(
                'Field of type ' . get_class($beGroupField) . ' is already in the collection!'
            );
        }

        $this->associativeBeGroupFields[get_class($beGroupField)] = $beGroupField;
    }

    public function getBeGroupField(int $position): ?BeGroupFieldInterface
    {
        return $this->beGroupFields[$position] ?? null;
    }

    /**
     * @return Iterator<BeGroupFieldInterface>
     */
    public function getIterator(): Iterator
    {
        return new class ($this) implements Iterator {

            private BeGroupFieldCollection $beGroupFieldCollection;
            private int $position = 0;

            public function __construct(BeGroupFieldCollection $beGroupFieldCollection)
            {
                $this->beGroupFieldCollection = $beGroupFieldCollection;
            }

            public function current(): ?BeGroupFieldInterface
            {
                return $this->beGroupFieldCollection->getBeGroupField($this->position);
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
                return $this->beGroupFieldCollection->getBeGroupField($this->position) instanceof BeGroupFieldInterface;
            }

            public function rewind(): void
            {
                $this->position = 0;
            }
        };
    }

    public function extend(BeGroupFieldCollection $beGroupFieldCollection): BeGroupFieldCollection
    {
        $extendedCollection = new self();
        $baseArray = $this->associativeBeGroupFields;

        foreach ($beGroupFieldCollection as $beGroupField) {
            if (isset($baseArray[get_class($beGroupField)])) {
                $baseGroupField = $baseArray[get_class($beGroupField)];
                $extendedCollection->add($baseGroupField->extend($beGroupField));
                unset($baseArray[get_class($beGroupField)]);
            } else {
                $extendedCollection->add($beGroupField);
            }
        }

        foreach ($baseArray as $remainingBGroupField) {
            $extendedCollection->add($remainingBGroupField);
        }

        return $extendedCollection;
    }
}
