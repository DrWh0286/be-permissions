<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Phpsu;

use PHPSu\Config\ConfigurationLoader;
use PHPSu\Controller;
use PHPSu\Helper\StringHelper;
use PHPSu\Options\SyncOptions;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class PhpsuSyncAdapter implements PhpsuSyncAdapterInterface
{
    public function syncBeGroups(string $source, OutputInterface $output): void
    {
        self::checkPhpsuInstallation();

        /** @var ConfigurationLoader $configurationLoader */
        $configurationLoader = GeneralUtility::makeInstance(ConfigurationLoader::class);
        /** @var Controller $controller */
        $controller = GeneralUtility::makeInstance(Controller::class);

        $configuration = $configurationLoader->getConfig();
        $instances = $configuration->getAppInstanceNames();

        $source = StringHelper::findStringInArray($source, $instances) ?: $source;
        $destination = StringHelper::findStringInArray('local', $instances) ?: 'local';

        $options = (new SyncOptions($source))
            ->setDestination($destination)
            ->setCurrentHost('')
            ->setDryRun(false)
            ->setAll(false)
            ->setTablesToSync('be_groups')
            ->setNoFiles(true)
            ->setNoDatabases(false);

        $controller->checkSshConnection($output, $configuration, $options);

        $controller->sync($output, $configuration, $options);
    }

    public function checkPhpsuInstallation(): void
    {
        self::theRealPhpsuInstallationCheck();
    }

    public static function staticCheckPhpsuInstallation(): void
    {
        self::theRealPhpsuInstallationCheck();
    }

    private static function theRealPhpsuInstallationCheck(): void
    {
        if (!class_exists(Controller::class) || !class_exists(ConfigurationLoader::class)) {
            throw new \RuntimeException('To run this command the package phpsu/phpsu needs to be installed!');
        }
    }
}
