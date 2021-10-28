<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Repository;

use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;

interface BeGroupRepositoryInterface
{
    public function findOneByIdentifier(Identifier $identifier): BeGroup;

    public function update(BeGroup $beGroup): void;
}
