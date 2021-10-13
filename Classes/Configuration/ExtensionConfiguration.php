<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as T3ExtensionConfiguration;

final class ExtensionConfiguration implements SingletonInterface
{
    /** @var array|null */
    private ?array $config = null;

    /**
     * @throws NoValueObjectConfiguredException
     */
    public function getClassNameByFieldName(string $fieldName): string
    {
        $config = $this->getConfig();

        if (!isset($config['valueObjectMapping'][$fieldName])) {
            throw new NoValueObjectConfiguredException('For field ' . $fieldName . ' no value object is configured.');
        }

        return $config['valueObjectMapping'][$fieldName];
    }

    private function getConfig(): array
    {
        if ($this->config === null) {
            /** @var T3ExtensionConfiguration $config */
            $config = GeneralUtility::makeInstance(T3ExtensionConfiguration::class);
            $this->config = $config->get('be_permissions');
        }

        return $this->config;
    }
}
