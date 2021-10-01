<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Repository;

use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Configuration\ConfigurationFileMissingException;
use Pluswerk\BePermissions\Value\Identifier;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BeGroupConfigurationRepository implements BeGroupConfigurationRepositoryInterface
{
    private string $beGroupConfigurationFileName = 'be_group.yaml';

    public function write(BeGroupConfiguration $beGroupConfiguration): void
    {
        $folder = $beGroupConfiguration->configPath() . '/be_groups/' . $beGroupConfiguration->identifier();
        $fileName = $folder . '/' . $this->beGroupConfigurationFileName;
        $content = Yaml::dump($beGroupConfiguration->asArray(), 99, 2);

        if (!file_exists($folder)) {
            GeneralUtility::mkdir_deep($folder);
        }

        GeneralUtility::writeFile($fileName, $content);
    }

    /**
     * @throws ConfigurationFileMissingException
     */
    public function load(Identifier $identifier, string $configPath): ?BeGroupConfiguration
    {
        $fileName = $configPath . '/be_groups/' . $identifier . '/' . $this->beGroupConfigurationFileName;

        if (!file_exists($fileName)) {
            throw new ConfigurationFileMissingException('No configuration file \'' . $fileName . '\' found!');
        }

        $loader = GeneralUtility::makeInstance(YamlFileLoader::class);
        $configuration = $loader->load(GeneralUtility::fixWindowsFilePath($fileName));

        return BeGroupConfiguration::createFromConfigurationArray($identifier, $configPath, $configuration);
    }
}
