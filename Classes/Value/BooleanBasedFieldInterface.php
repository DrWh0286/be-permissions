<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

interface BooleanBasedFieldInterface extends BeGroupFieldInterface
{
    public static function createFromYamlConfiguration(bool $configValue): BooleanBasedFieldInterface;

    public function yamlConfigurationValue(): bool;
}
