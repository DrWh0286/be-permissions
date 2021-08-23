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
    private static string $beGroupConfigurationFileName = 'be_group.yaml';

    private Identifier $identifier;
    private string $configPath;
    private array $config;

    public function __construct(Identifier $identifier, string $configPath, array $config = [])
    {
        $this->identifier = $identifier;
        $this->configPath = $configPath;
        $this->config = $config;
    }

    /**
     * @param Identifier $identifier
     * @param string $configPath
     * @return BeGroupConfiguration
     * @throws ConfigurationFileMissingException
     */
    public static function load(Identifier $identifier, string $configPath): BeGroupConfiguration
    {
        $fileName = $configPath . '/' . $identifier . '/' . self::$beGroupConfigurationFileName;

        if (!file_exists($fileName)) {
            throw new ConfigurationFileMissingException('No configuration file \'' . $fileName . '\' found!');
        }

        $loader = GeneralUtility::makeInstance(YamlFileLoader::class);
        $configuration = $loader->load(GeneralUtility::fixWindowsFilePath($fileName));

        return new self($identifier, $configPath, $configuration);
    }

    public static function createFromBeGroup(BeGroup $beGroup, string $configPath): BeGroupConfiguration
    {
        $config = [
            'title' => $beGroup->title(),
            'non_exclude_fields' => $beGroup->nonExcludeFields()
        ];

        return new self($beGroup->identifier(), $configPath, $config);
    }

    public function write(): void
    {
        $folder = $this->configPath . '/' . $this->identifier;
        $fileName = $folder . '/' . self::$beGroupConfigurationFileName;
        $content = Yaml::dump($this->config, 99, 2);

        if (!file_exists($folder)) {
            GeneralUtility::mkdir_deep($folder);
        }

        GeneralUtility::writeFile($fileName, $content);
    }

    public function rawConfiguration(): array
    {
        return $this->config;
    }
}
