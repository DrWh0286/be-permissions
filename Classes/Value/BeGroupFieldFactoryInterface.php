<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

interface BeGroupFieldFactoryInterface
{
    public function buildFromFieldNameAndYamlValue(string $fieldName, $value): ?BeGroupFieldInterface;

    public function buildFromFieldNameAndDatabaseValue(string $fieldName, string $value): ?BeGroupFieldInterface;
}
