<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Repository;

use Pluswerk\BePermissions\Collection\BeGroupCollection;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;

interface BeGroupRepositoryInterface
{
    public function findOneByIdentifier(Identifier $identifier): ?BeGroup;

    public function findOneByUid(int $uid): ?BeGroup;

    public function update(BeGroup $beGroup): void;

    public function add(BeGroup $beGroup): void;

    public function findAllForBulkExport(): BeGroupCollection;

    public function addOrUpdateBeGroups(BeGroupCollection $beGroups): void;

    public function addOrUpdateBeGroup(BeGroup $beGroup): void;
}
