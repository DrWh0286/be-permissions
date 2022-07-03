<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Repository;

use DirectoryIterator;
use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use SebastianHofer\BePermissions\Collection\DuplicateBeGroupFieldException;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Configuration\ConfigurationFileMissingException;
use SebastianHofer\BePermissions\Value\Identifier;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class BeGroupConfigurationRepository implements BeGroupConfigurationRepositoryInterface
{
    private string $beGroupConfigurationFileName = 'be_group.yaml';
    private BeGroupFieldCollectionBuilder $beGroupFieldCollectionBuilder;

    public function __construct(BeGroupFieldCollectionBuilder $beGroupFieldCollectionBuilder)
    {
        $this->beGroupFieldCollectionBuilder = $beGroupFieldCollectionBuilder;
    }

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
     * @throws ConfigurationFileMissingException|DuplicateBeGroupFieldException
     */
    public function load(Identifier $identifier, string $configPath): BeGroupConfiguration
    {
        $fileName = $configPath . '/be_groups/' . $identifier . '/' . $this->beGroupConfigurationFileName;

        if (!file_exists($fileName)) {
            throw new ConfigurationFileMissingException('No configuration file \'' . $fileName . '\' found!');
        }

        /** @var YamlFileLoader $loader */
        $loader = GeneralUtility::makeInstance(YamlFileLoader::class);
        $configuration = $loader->load(GeneralUtility::fixWindowsFilePath($fileName));

        $collection = $this->beGroupFieldCollectionBuilder->buildFromConfigurationArray($configuration);

        return new BeGroupConfiguration($identifier, $configPath, $collection);
    }

    public function loadYamlString(Identifier $identifier, string $configPath): string
    {
        $fileName = $configPath . '/be_groups/' . $identifier . '/' . $this->beGroupConfigurationFileName;

        if (!file_exists($fileName)) {
            throw new ConfigurationFileMissingException('No configuration file \'' . $fileName . '\' found!');
        }

        /** @var YamlFileLoader $loader */
        $loader = GeneralUtility::makeInstance(YamlFileLoader::class);
        $configuration = $loader->load(GeneralUtility::fixWindowsFilePath($fileName));

        return Yaml::dump($configuration, 99, 2);
    }

    public function loadAll(string $configPath): array
    {
        $directoryIterator = new DirectoryIterator($configPath . '/be_groups/');

        $beGroupConfigurations = [];

        /** @var DirectoryIterator $directory */
        foreach ($directoryIterator as $directory) {
            try {
                $beGroupConfigurations[] = $this->load(new Identifier($directory->getFilename()), $configPath);
            } catch (ConfigurationFileMissingException $e) {
                continue;
            }
        }

        return $beGroupConfigurations;
    }
}
