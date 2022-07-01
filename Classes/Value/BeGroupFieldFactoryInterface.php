<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Value;

interface BeGroupFieldFactoryInterface
{
    /**
     * @param string $fieldName
     * @param mixed $value
     * @return BeGroupFieldInterface|null
     */
    public function buildFromFieldNameAndYamlValue(string $fieldName, $value): ?BeGroupFieldInterface;

    public function buildFromFieldNameAndDatabaseValue(string $fieldName, string $value): ?BeGroupFieldInterface;
}
