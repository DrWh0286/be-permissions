<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Configuration;

use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BeGroupConfiguration
{
    private Identifier $identifier;
    private string $configPath;
    private NonExcludeFields $nonExcludeFields;
    private string $title;
    private ExplicitAllowDeny $explicitAllowDeny;

    private function __construct(Identifier $identifier, string $configPath, string $title, NonExcludeFields $nonExcludeFields, ExplicitAllowDeny $explicitAllowDeny)
    {
        $this->identifier = $identifier;
        $this->configPath = $configPath;
        $this->nonExcludeFields = $nonExcludeFields;
        $this->title = $title;
        $this->explicitAllowDeny = $explicitAllowDeny;
    }

    public static function createFromBeGroup(BeGroup $beGroup, string $configPath): BeGroupConfiguration
    {
        return new self($beGroup->identifier(), $configPath, $beGroup->title(), $beGroup->nonExcludeFields(), $beGroup->explicitAllowDeny());
    }

    public static function createFromConfigurationArray(Identifier $identifier, string $configPath, array $configuration): BeGroupConfiguration
    {
        if (empty($configuration['title'])) {
            throw new \RuntimeException('A ' . __CLASS__ . ' needs a title!');
        }

        $title = $configuration['title'] ?? '';
        $nonExcludeFields = NonExcludeFields::createFromConfigurationArray($configuration['non_exclude_fields'] ?? []);
        $explicitAllowDeny = ExplicitAllowDeny::createFromConfigurationArray($configuration['explicit_allowdeny'] ?? []);

        return new self($identifier, $configPath, $title, $nonExcludeFields, $explicitAllowDeny);
    }

    public function title(): string
    {
        return $this->title;
    }

    public function nonExcludeFields(): NonExcludeFields
    {
        return $this->nonExcludeFields;
    }

    public function explicitAllowDeny(): ExplicitAllowDeny
    {
        return $this->explicitAllowDeny;
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

    public function asArray(): array
    {
        $array = [];
        $array['title'] = $this->title;

        if (!empty($this->nonExcludeFields->asArray())) {
            $array['non_exclude_fields'] = $this->nonExcludeFields->asArray();
        }

        if (!empty($this->explicitAllowDeny->asArray())) {
            $array['explicit_allowdeny'] = $this->explicitAllowDeny->asArray();
        }

        return $array;
    }
}
