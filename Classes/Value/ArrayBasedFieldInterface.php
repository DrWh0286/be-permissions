<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

interface ArrayBasedFieldInterface extends BeGroupFieldInterface
{
    public static function createFromYamlConfiguration($configValue): ArrayBasedFieldInterface;

    public function yamlConfigurationValue(): array;
}
