<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Repository;

use SebastianHofer\BePermissions\Collection\BeGroupConfigurationCollection;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Configuration\ConfigurationFileMissingException;
use SebastianHofer\BePermissions\Value\Identifier;

interface BeGroupConfigurationRepositoryInterface
{
    public function write(BeGroupConfiguration $beGroupConfiguration): void;

    /**
     * @throws ConfigurationFileMissingException
     */
    public function load(Identifier $identifier, string $configPath): BeGroupConfiguration;

    /**
     * @param string $configPath
     * @return BeGroupConfigurationCollection
     */
    public function loadAll(string $configPath): BeGroupConfigurationCollection;

    /**
     * @throws ConfigurationFileMissingException
     */
    public function loadYamlString(Identifier $identifier, string $configPath): string;
}
