<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

interface ExtensionConfigurationInterface
{
    /**
     * @throws NoValueObjectConfiguredException
     */
    public function getClassNameByFieldName(string $fieldName): string;
    public function getProductionHost(): string;
    public function getApiToken(): string;
}
