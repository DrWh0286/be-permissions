<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Api;

use Pluswerk\BePermissions\Collection\BeGroupCollection;

interface ApiInterface
{
    /**
     * @return BeGroupCollection
     */
    public function fetchAllSynchronizedBeGroups(): BeGroupCollection;
}
