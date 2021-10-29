<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

interface StringBasedFieldInterface extends BeGroupFieldInterface
{
    public static function createFromYamlConfiguration(string $configValue): StringBasedFieldInterface;

    public function yamlConfigurationValue(): string;
}
