<?php
declare(strict_types=1);

namespace Pluswerk\BePermissions\Value;

interface BeGroupFieldInterface
{
    public static function createFromDBValue(string $dbValue): BeGroupFieldInterface;

    public static function createFromConfigurationArray(array $confArray): BeGroupFieldInterface;

    public function asArray(): array;

    public function extend(BeGroupFieldInterface $beGroupField): BeGroupFieldInterface;

    public function getFieldName(): string;
}