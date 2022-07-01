<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Api;

use SebastianHofer\BePermissions\Collection\BeGroupCollection;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Value\Identifier;

interface ApiInterface
{
    /**
     * @return BeGroupCollection
     */
    public function fetchAllSynchronizedBeGroups(): BeGroupCollection;

    /**
     * @param Identifier $identifier
     * @return BeGroup|null
     */
    public function fetchBeGroupsByIdentifier(Identifier $identifier): ?BeGroup;
}
