<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

interface ArrayBasedFieldInterface extends BeGroupFieldInterface
{
    /** @param array<mixed> $configValue */
    public static function createFromYamlConfiguration(array $configValue): ArrayBasedFieldInterface;

    /** @return array<mixed> */
    public function yamlConfigurationValue(): array;
}
