<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

interface BeGroupFieldInterface
{
    public static function createFromDBValue(string $dbValue): BeGroupFieldInterface;

    /** @return mixed */
    public function yamlConfigurationValue();

    public function extend(BeGroupFieldInterface $beGroupField): BeGroupFieldInterface;

    public function getFieldName(): string;

    public function __toString(): string;
}
