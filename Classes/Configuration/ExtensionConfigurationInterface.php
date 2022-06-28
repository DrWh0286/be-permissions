<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

use Pluswerk\BePermissions\Value\Source;

interface ExtensionConfigurationInterface
{
    /**
     * @throws NoValueObjectConfiguredException
     */
    public function getClassNameByFieldName(string $fieldName): string;
    public function getProductionHost(): string;
    public function getHostBySource(Source $source): string;
    public function getApiToken(): string;
}
