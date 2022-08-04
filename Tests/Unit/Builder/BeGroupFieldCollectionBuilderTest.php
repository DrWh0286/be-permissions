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

namespace SebastianHofer\BePermissions\Tests\Unit\Builder;

use SebastianHofer\BePermissions\Collection\BeGroupFieldCollection;
use SebastianHofer\BePermissions\Value\BeGroupFieldFactoryInterface;
use SebastianHofer\BePermissions\Value\NonExcludeFields;
use SebastianHofer\BePermissions\Value\TablesSelect;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilder;

/**
 * @covers \SebastianHofer\BePermissions\Builder\BeGroupFieldCollectionBuilder
 */
final class BeGroupFieldCollectionBuilderTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['be_permissions'] = [];
    }

    /**
     * @test
     */
    public function a_be_group_field_collection_is_built_from_database_array(): void //phpcs:ignore
    {
        $fieldFactory = $this->createMock(BeGroupFieldFactoryInterface::class);
        $builder = new BeGroupFieldCollectionBuilder($fieldFactory);
        $dbValues = [
            'non_exclude_fields' => 'pages:media,pages:hidden,tt_content:pages,tt_content:date',
            'tables_select' => 'pages,tt_content,tx_news_domain_model_link,tx_news_domain_model_news'
        ];

        $nonExcludeFields = NonExcludeFields::createFromDBValue('pages:media,pages:hidden,tt_content:pages,tt_content:date');
        $tablesSelect = TablesSelect::createFromDBValue('tt_content,tx_news_domain_model_link,pages,tx_news_domain_model_news');
        $expectedCollection = new BeGroupFieldCollection();
        $expectedCollection->add($nonExcludeFields);
        $expectedCollection->add($tablesSelect);

        $fieldFactory->expects($this->exactly(2))
            ->method('buildFromFieldNameAndDatabaseValue')
            ->withConsecutive([$nonExcludeFields->getFieldName(), 'pages:media,pages:hidden,tt_content:pages,tt_content:date'], [$tablesSelect->getFieldName(), (string)$tablesSelect])
            ->willReturnOnConsecutiveCalls($nonExcludeFields, $tablesSelect);

        $collection = $builder->buildFromDatabaseValues($dbValues);

        $this->assertEquals($expectedCollection, $collection);
    }

    /**
     * @test
     */
    public function a_be_group_collection_is_built_from_yaml_configuration_array(): void //phpcs:ignore
    {
        $fieldFactory = $this->createMock(BeGroupFieldFactoryInterface::class);
        $builder = new BeGroupFieldCollectionBuilder($fieldFactory);

        $configurationArray = [
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ],
            'tables_select' => ['pages','tt_content','tx_news_domain_model_link','tx_news_domain_model_news']
        ];

        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => [
                    'title',
                    'media'
                ]
            ]
        );
        $tablesSelect = TablesSelect::createFromYamlConfiguration(
            ['pages','tt_content','tx_news_domain_model_link','tx_news_domain_model_news']
        );

        $expectedCollection = new BeGroupFieldCollection();
        $expectedCollection->add($nonExcludeFields);
        $expectedCollection->add($tablesSelect);

        $fieldFactory->expects($this->exactly(2))
            ->method('buildFromFieldNameAndYamlValue')
            ->withConsecutive(['non_exclude_fields', $configurationArray['non_exclude_fields']], ['tables_select', $configurationArray['tables_select']])
            ->willReturnOnConsecutiveCalls($nonExcludeFields, $tablesSelect);

        $collection = $builder->buildFromConfigurationArray($configurationArray);

        $this->assertEquals($expectedCollection, $collection);
    }
}
