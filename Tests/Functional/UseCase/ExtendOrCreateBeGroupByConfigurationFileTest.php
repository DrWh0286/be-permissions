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
use Pluswerk\BePermissions\UseCase\ExtendOrCreateBeGroupByConfigurationFile;
use Pluswerk\BePermissions\Value\AllowedLanguages;
use Pluswerk\BePermissions\Value\AvailableWidgets;
use Pluswerk\BePermissions\Value\BeGroupFieldFactory;
use Pluswerk\BePermissions\Value\CodeManagedGroup;
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

/**
 * @covers \Pluswerk\BePermissions\UseCase\ExtendOrCreateBeGroupByConfigurationFile
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
final class ExtendOrCreateBeGroupByConfigurationFileTest extends FunctionalTestCase
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
    public function a_connected_configuration_can_extend_the_be_group_record(): void //phpcs:ignore
    {
        $this->importDataSet(__DIR__ . '/Fixtures/be_groups.xml');

        // Prepare file
        $identifier = new Identifier('test-group');

        $collection = new BeGroupFieldCollection();
        $collection->add(Title::createFromYamlConfiguration('Some new group title'));
        $collection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'abstract'
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

        /** @var ExtendOrCreateBeGroupByConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(ExtendOrCreateBeGroupByConfigurationFile::class);

        $useCase->extendGroup('test-group');

        /** @var BeGroupRepository $repo */
        $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

        $actualBeGroup = $repo->findOneByIdentifier(new Identifier('test-group'));

        $expectedCollection = new BeGroupFieldCollection();

        $expectedCollection->add(Title::createFromYamlConfiguration('Some group title'));
        $expectedCollection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'hidden',
                    'abstract'
                ],
                'tt_content' => [
                    'hidden',
                    'some_additiona_field',
                    'another_field'
                ]
            ]
        ));
        $expectedCollection->add(ExplicitAllowDeny::createFromYamlConfiguration([]));
        $expectedCollection->add(AllowedLanguages::createFromYamlConfiguration([]));
        $expectedCollection->add(DbMountpoints::createFromYamlConfiguration([]));
        $expectedCollection->add(PageTypesSelect::createFromYamlConfiguration([]));
        $expectedCollection->add(TablesSelect::createFromYamlConfiguration([]));
        $expectedCollection->add(TablesModify::createFromYamlConfiguration([]));
        $expectedCollection->add(GroupMods::createFromYamlConfiguration([]));
        $expectedCollection->add(AvailableWidgets::createFromYamlConfiguration([]));
        $expectedCollection->add(MfaProviders::createFromYamlConfiguration([]));
        $expectedCollection->add(FileMountpoints::createFromYamlConfiguration([]));
        $expectedCollection->add(FilePermissions::createFromYamlConfiguration([]));
        $expectedCollection->add(CategoryPerms::createFromYamlConfiguration([]));
        $expectedCollection->add(CodeManagedGroup::createFromYamlConfiguration(true));
        $expectedCollection->add(DeployProcessing::createFromDBValue(''));
        $expectedBeGroup = new BeGroup(
            $identifier,
            $expectedCollection
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
                    'title',
                    'abstract'
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

        /** @var ExtendOrCreateBeGroupByConfigurationFile $useCase */
        $useCase = GeneralUtility::makeInstance(ExtendOrCreateBeGroupByConfigurationFile::class);

        $useCase->extendGroup('test-group');

        /** @var BeGroupRepository $repo */
        $repo = GeneralUtility::makeInstance(BeGroupRepository::class);

        $actualBeGroup = $repo->findOneByIdentifier(new Identifier('test-group'));

        $expectedCollection = new BeGroupFieldCollection();

        $expectedCollection->add(Title::createFromYamlConfiguration('Some new group title'));
        $expectedCollection->add(NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'abstract'
                ],
                'tt_content' => [
                    'some_additiona_field',
                    'another_field',
                    'hidden'
                ]
            ]
        ));
        $expectedCollection->add(ExplicitAllowDeny::createFromYamlConfiguration([]));
        $expectedCollection->add(AllowedLanguages::createFromYamlConfiguration([]));
        $expectedCollection->add(DbMountpoints::createFromYamlConfiguration([]));
        $expectedCollection->add(PageTypesSelect::createFromYamlConfiguration([]));
        $expectedCollection->add(TablesSelect::createFromYamlConfiguration([]));
        $expectedCollection->add(TablesModify::createFromYamlConfiguration([]));
        $expectedCollection->add(GroupMods::createFromYamlConfiguration([]));
        $expectedCollection->add(AvailableWidgets::createFromYamlConfiguration([]));
        $expectedCollection->add(MfaProviders::createFromYamlConfiguration([]));
        $expectedCollection->add(FileMountpoints::createFromYamlConfiguration([]));
        $expectedCollection->add(FilePermissions::createFromYamlConfiguration([]));
        $expectedCollection->add(CategoryPerms::createFromYamlConfiguration([]));
        $expectedCollection->add(CodeManagedGroup::createFromYamlConfiguration(true));
        $expectedCollection->add(DeployProcessing::createFromDBValue(''));
        $expectedBeGroup = new BeGroup(
            $identifier,
            $expectedCollection
        );

        $this->assertEquals($expectedBeGroup, $actualBeGroup);
    }
}
