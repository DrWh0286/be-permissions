<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Api;

use Pluswerk\BePermissions\Collection\BeGroupCollection;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;

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
