<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

use TYPO3\CMS\Core\Http\Uri;

interface ExtensionConfigurationInterface
{
    /**
     * @throws NoValueObjectConfiguredException
     */
    public function getClassNameByFieldName(string $fieldName): string;
    public function getApiToken(): string;
    public function getApiUri(): Uri;
}
