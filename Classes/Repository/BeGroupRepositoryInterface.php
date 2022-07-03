<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Repository;

use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;

interface BeGroupRepositoryInterface
{
    public function findOneByIdentifier(Identifier $identifier): ?BeGroup;

    /**
     * @return string[]
     */
    public function findOneByIdentifierRaw(Identifier $identifier): array;

    public function findOneByUid(int $uid): ?BeGroup;

    public function update(BeGroup $beGroup): void;

    public function add(BeGroup $beGroup): void;

    public function findAllCodeManaged(): BeGroupCollection;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllCodeManagedRaw(): array;

    public function addOrUpdateBeGroups(BeGroupCollection $beGroups): void;

    public function addOrUpdateBeGroup(BeGroup $beGroup): void;

    public function loadYamlString(Identifier $identifier): string;
}
