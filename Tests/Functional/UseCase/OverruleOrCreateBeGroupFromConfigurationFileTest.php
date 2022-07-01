<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Functional\UseCase;

use Pluswerk\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Configuration\BeGroupConfiguration;
use Pluswerk\BePermissions\Configuration\ExtensionConfiguration;
use Pluswerk\BePermissions\Model\BeGroup;
use Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository;
use Pluswerk\BePermissions\Repository\BeGroupRepository;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\AvailableWidgets;
use Pluswerk\BePermissions\Value\BeGroupFieldFactory;
use Pluswerk\BePermissions\Value\BulkExport;
use Pluswerk\BePermissions\Value\CategoryPerms;
use Pluswerk\BePermissions\Value\DbMountpoints;
use Pluswerk\BePermissions\Value\DeployProcessing;
use Pluswerk\BePermissions\Value\ExplicitAllowDeny;
use Pluswerk\BePermissions\Value\FileMountpoints;
use Pluswerk\BePermissions\Value\FilePermissions;
use Pluswerk\BePermissions\Value\GroupMods;
use Pluswerk\BePermissions\Value\Identifier;
use Pluswerk\BePermissions\Value\MfaProviders;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Pluswerk\BePermissions\Value\PageTypesSelect;
use Pluswerk\BePermissions\Value\TablesModify;
use Pluswerk\BePermissions\Value\TablesSelect;
use Pluswerk\BePermissions\Value\Title;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Pluswerk\BePermissions\UseCase\OverruleOrCreateBeGroupFromConfigurationFile;

/**
 * @covers \Pluswerk\BePermissions\UseCase\OverruleOrCreateBeGroupFromConfigurationFile
 * @uses \Pluswerk\BePermissions\Configuration\BeGroupConfiguration
 * @uses \Pluswerk\BePermissions\Model\BeGroup
 * @uses \Pluswerk\BePermissions\Repository\BeGroupConfigurationRepository
 * @uses \Pluswerk\BePermissions\Repository\BeGroupRepository
 * @uses \Pluswerk\BePermissions\Value\AllowedLanguages
 * @uses \Pluswerk\BePermissions\Value\ExplicitAllowDeny
 * @uses \Pluswerk\BePermissions\Value\Identifier
 * @uses \Pluswerk\BePermissions\Value\NonExcludeFields
 * @uses \TYPO3\CMS\Core\Core\Environment
 * @uses \TYPO3\CMS\Core\Utility\GeneralUtility
 */
final class OverruleOrCreateBeGroupFromConfigurationFileTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/be_permissions'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [];
    }

    /**
     * @test
     */
    public function a_connected_configuration_can_overrule_the_be_group_record(): void //phpcs:ignore
    {
        $this->importDataSet(__DIR__ . '/Fixtures/be_groups.xml');

        // Prepare file
        $identifier = new Identifier('test-group');

        $collection = new BeGroupFieldCollection();
        $collection->add(Title::createFromYamlConfiguration('Some new group title'));
        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]
        ));
        $collection->add(BulkExport::createFromYamlConfiguration(true));

        $configuration = new BeGroupConfiguration($identifier, Environment::getConfigPath(), $collection);
        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
        $repository->write($configuration);

        /** @var OverruleOrCreateBeGroupFromConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(OverruleOrCreateBeGroupFromConfigurationFile::class);

        $useCase->overruleGroup('test-group');

        /** @var BeGroupRepository $repo */
        $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

        $actualBeGroup = $repo->findOneByIdentifier(new Identifier('test-group'));

        $collection = new BeGroupFieldCollection();
        $collection->add(Title::createFromYamlConfiguration('Some new group title'));
        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]
        ));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration([]));
        $collection->add(AllowedLanguages::createFromYamlConfiguration([]));
        $collection->add(DbMountpoints::createFromYamlConfiguration([]));
        $collection->add(PageTypesSelect::createFromYamlConfiguration([]));
        $collection->add(TablesSelect::createFromYamlConfiguration([]));
        $collection->add(TablesModify::createFromYamlConfiguration([]));
        $collection->add(GroupMods::createFromYamlConfiguration([]));
        $collection->add(AvailableWidgets::createFromYamlConfiguration([]));
        $collection->add(MfaProviders::createFromYamlConfiguration([]));
        $collection->add(FileMountpoints::createFromYamlConfiguration([]));
        $collection->add(FilePermissions::createFromYamlConfiguration([]));
        $collection->add(CategoryPerms::createFromYamlConfiguration([]));
        $collection->add(BulkExport::createFromYamlConfiguration(true));
        $collection->add(DeployProcessing::createFromDBValue(''));

        $expectedBeGroup = new BeGroup(
            $identifier,
            $collection
        );

        $this->assertEquals($expectedBeGroup, $actualBeGroup);
    }

    /**
     * @test
     */
    public function if_a_group_does_not_exist_it_is_created(): void //phpcs:ignore
    {
        // Prepare file
        $identifier = new Identifier('test-group');

        $collection = new BeGroupFieldCollection();
        $collection->add(Title::createFromYamlConfiguration('Some new group title'));
        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]
        ));
        $collection->add(BulkExport::createFromYamlConfiguration(true));

        $configuration = new BeGroupConfiguration($identifier, Environment::getConfigPath(), $collection);
        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
        $repository->write($configuration);

        /** @var OverruleOrCreateBeGroupFromConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(OverruleOrCreateBeGroupFromConfigurationFile::class);

        $useCase->overruleGroup('test-group');

        /** @var BeGroupRepository $repo */
        $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

        $actualBeGroup = $repo->findOneByIdentifier(new Identifier('test-group'));

        $collection = new BeGroupFieldCollection();
        $collection->add(Title::createFromYamlConfiguration('Some new group title'));
        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]
        ));
        $collection->add(ExplicitAllowDeny::createFromYamlConfiguration([]));
        $collection->add(AllowedLanguages::createFromYamlConfiguration([]));
        $collection->add(DbMountpoints::createFromYamlConfiguration([]));
        $collection->add(PageTypesSelect::createFromYamlConfiguration([]));
        $collection->add(TablesSelect::createFromYamlConfiguration([]));
        $collection->add(TablesModify::createFromYamlConfiguration([]));
        $collection->add(GroupMods::createFromYamlConfiguration([]));
        $collection->add(AvailableWidgets::createFromYamlConfiguration([]));
        $collection->add(MfaProviders::createFromYamlConfiguration([]));
        $collection->add(FileMountpoints::createFromYamlConfiguration([]));
        $collection->add(FilePermissions::createFromYamlConfiguration([]));
        $collection->add(CategoryPerms::createFromYamlConfiguration([]));
        $collection->add(BulkExport::createFromYamlConfiguration(true));
        $collection->add(DeployProcessing::createFromDBValue(''));

        $expectedBeGroup = new BeGroup(
            $identifier,
            $collection
        );

        $this->assertEquals($expectedBeGroup, $actualBeGroup);
    }
}
