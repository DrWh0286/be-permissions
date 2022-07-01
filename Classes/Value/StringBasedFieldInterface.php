<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

interface StringBasedFieldInterface extends BeGroupFieldInterface
{
    public static function createFromYamlConfiguration(string $configValue): StringBasedFieldInterface;

    public function yamlConfigurationValue(): string;
}
