<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Repository;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;

interface BeGroupConfigurationRepositoryInterface
{
    public function write(BeGroupConfiguration $beGroupConfiguration): void;
}
