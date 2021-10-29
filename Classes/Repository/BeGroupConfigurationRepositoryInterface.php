<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Repository;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Configuration\ConfigurationFileMissingException;
use Pluswerk\BePermissions\Value\Identifier;

interface BeGroupConfigurationRepositoryInterface
{
    public function write(BeGroupConfiguration $beGroupConfiguration): void;

    /**
     * @throws ConfigurationFileMissingException
     */
    public function load(Identifier $identifier, string $configPath): BeGroupConfiguration;
}
