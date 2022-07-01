<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Repository;

use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;

interface BeGroupRepositoryInterface
{
    public function findOneByIdentifier(Identifier $identifier): ?BeGroup;

    public function findOneByUid(int $uid): ?BeGroup;

    public function update(BeGroup $beGroup): void;

    public function add(BeGroup $beGroup): void;

    public function findAllCodeManaged(): BeGroupCollection;

    public function addOrUpdateBeGroups(BeGroupCollection $beGroups): void;

    public function addOrUpdateBeGroup(BeGroup $beGroup): void;
}
