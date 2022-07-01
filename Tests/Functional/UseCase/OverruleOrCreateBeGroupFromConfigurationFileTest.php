<?php

declare(strict_types=1);

namespace SebastianHofer\BePermissions\Tests\Functional\UseCase;

use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Configuration\ExtensionConfiguration;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepository;
use SebastianHofer\BePermissions\Repository\BeGroupRepository;
use SebastianHofer\BePermissions\Value\AllowedLanguages;
use SebastianHofer\BePermissions\Value\AvailableWidgets;
use SebastianHofer\BePermissions\Value\BeGroupFieldFactory;
use SebastianHofer\BePermissions\Value\CodeManagedGroup;
use SebastianHofer\BePermissions\Value\CategoryPerms;
use SebastianHofer\BePermissions\Value\DbMountpoints;
use SebastianHofer\BePermissions\Value\DeployProcessing;
use SebastianHofer\BePermissions\Value\ExplicitAllowDeny;
use SebastianHofer\BePermissions\Value\FileMountpoints;
use SebastianHofer\BePermissions\Value\FilePermissions;
use SebastianHofer\BePermissions\Value\GroupMods;
use SebastianHofer\BePermissions\Value\Identifier;
use SebastianHofer\BePermissions\Value\MfaProviders;
use SebastianHofer\BePermissions\Value\NonExcludeFields;
use SebastianHofer\BePermissions\Value\PageTypesSelect;
use SebastianHofer\BePermissions\Value\TablesModify;
use SebastianHofer\BePermissions\Value\TablesSelect;
use SebastianHofer\BePermissions\Value\Title;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use SebastianHofer\BePermissions\UseCase\OverruleOrCreateBeGroupFromConfigurationFile;

/**
 * @covers \SebastianHofer\BePermissions\UseCase\OverruleOrCreateBeGroupFromConfigurationFile
 * @uses \SebastianHofer\BePermissions\Configuration\BeGroupConfiguration
 * @uses \SebastianHofer\BePermissions\Model\BeGroup
 * @uses \SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepository
 * @uses \SebastianHofer\BePermissions\Repository\BeGroupRepository
 * @uses \SebastianHofer\BePermissions\Value\AllowedLanguages
 * @uses \SebastianHofer\BePermissions\Value\ExplicitAllowDeny
 * @uses \SebastianHofer\BePermissions\Value\Identifier
 * @uses \SebastianHofer\BePermissions\Value\NonExcludeFields
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
        $collection->add(CodeManagedGroup::createFromYamlConfiguration(true));

        $configuration = new BeGroupConfiguration($identifier, Environment::getConfigPath(), $collection);
        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
        $repository->write($configuration);

        /** @var OverruleOrCreateBeGroupFromConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(OverruleOrCreateBeGroupFromConfigurationFile::class);

        $useCase->overruleGroup(new Identifier('test-group'));

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
        $collection->add(CodeManagedGroup::createFromYamlConfiguration(true));
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
        $collection->add(CodeManagedGroup::createFromYamlConfiguration(true));

        $configuration = new BeGroupConfiguration($identifier, Environment::getConfigPath(), $collection);
        $extConfig = new ExtensionConfiguration();
        $factory = new BeGroupFieldFactory($extConfig);
        $builder = new BeGroupFieldCollectionBuilder($factory);
        $repository = new BeGroupConfigurationRepository($builder);
        $repository->write($configuration);

        /** @var OverruleOrCreateBeGroupFromConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(OverruleOrCreateBeGroupFromConfigurationFile::class);

        $useCase->overruleGroup(new Identifier('test-group'));

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
        $collection->add(CodeManagedGroup::createFromYamlConfiguration(true));
        $collection->add(DeployProcessing::createFromDBValue(''));

        $expectedBeGroup = new BeGroup(
            $identifier,
            $collection
        );

        $this->assertEquals($expectedBeGroup, $actualBeGroup);
    }
}
