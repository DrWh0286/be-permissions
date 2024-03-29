<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "be_permissions".
 *
 * Copyright (C) 2022 Sebastian Hofer <sebastian.hofer@s-hofer.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace SebastianHofer\BePermissions\Tests\Functional\UseCase;

use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilder;
use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Configuration\BeGroupConfiguration;
use SebastianHofer\BePermissions\Configuration\ExtensionConfiguration;
use SebastianHofer\BePermissions\Model\BeGroup;
use SebastianHofer\BePermissions\Repository\BeGroupConfigurationRepository;
use SebastianHofer\BePermissions\Repository\BeGroupRepository;
use SebastianHofer\BePermissions\UseCase\ExtendOrCreateBeGroupByConfigurationFile;
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
use SebastianHofer\BePermissions\Value\SubGroup;
use SebastianHofer\BePermissions\Value\TablesModify;
use SebastianHofer\BePermissions\Value\TablesSelect;
use SebastianHofer\BePermissions\Value\Title;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @covers \SebastianHofer\BePermissions\UseCase\ExtendOrCreateBeGroupByConfigurationFile
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

        $useCase->extendGroup(new Identifier('test-group'));

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
        $expectedCollection->add(SubGroup::createFromYamlConfiguration([]));
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

        $useCase->extendGroup(new Identifier('test-group'));

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
        $expectedCollection->add(SubGroup::createFromYamlConfiguration([]));
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
