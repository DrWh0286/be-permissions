<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\Identifier;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BeGroupConfiguration
{
    private Identifier $identifier;
    private string $configPath;
    private array $config;

    public function __construct(Identifier $identifier, string $configPath, array $config = [])
    {
        $this->identifier = $identifier;
        $this->configPath = $configPath;
        $this->config = $config;
    }

    public static function createFromBeGroup(BeGroup $beGroup, string $configPath): BeGroupConfiguration
    {
        $config = [
            'title' => $beGroup->title(),
            'non_exclude_fields' => $beGroup->nonExcludeFields()
        ];

        return new self($beGroup->identifier(), $configPath, $config);
    }

    public function rawConfiguration(): array
    {
        return $this->config;
    }

    public function nonExcludeFields(): array
    {
        return $this->config['non_exclude_fields'] ?? [];
    }

    /**
     * @return array
     */
    public function config(): array
    {
        return $this->config;
    }

    /**
     * @return Identifier
     */
    public function identifier(): Identifier
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function configPath(): string
    {
        return $this->configPath;
    }
}
