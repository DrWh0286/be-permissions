<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Builder;

use Pluswerk\BePermissions\Collection\BeGroupFieldCollection;
use Pluswerk\BePermissions\Configuration\ExtensionConfiguration;
use Pluswerk\BePermissions\Value\NonExcludeFields;
use Pluswerk\BePermissions\Value\TablesSelect;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Builder\BeGroupFieldCollectionBuilder;

/**
 * @covers \Pluswerk\BePermissions\Builder\BeGroupFieldCollectionBuilder
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
    public function a_be_group_field_collection_is_built_from_database_array(): void
    {
        $config = new ExtensionConfiguration();
        $builder = new BeGroupFieldCollectionBuilder($config);
        $dbValues = [
            'title' => 'be group',
            'non_exclude_fields' => 'pages:media,pages:hidden,tt_content:pages,tt_content:date',
            'tables_select' => 'pages,tt_content,tx_news_domain_model_link,tx_news_domain_model_news'
        ];

        $collection = $builder->buildFromDatabaseValues($dbValues);

        $nonExcludeFields = NonExcludeFields::createFromDBValue('pages:media,pages:hidden,tt_content:pages,tt_content:date');
        $tablesSelect = TablesSelect::createFromDBValue('pages,tt_content,tx_news_domain_model_link,tx_news_domain_model_news');
        $expectedCollection = new BeGroupFieldCollection();
        $expectedCollection->add($nonExcludeFields);
        $expectedCollection->add($tablesSelect);

        $this->assertEquals($expectedCollection, $collection);
    }

    /**
     * @test
     */
    public function a_be_group_collection_is_built_from_yaml_configuration_array(): void
    {
        $config = new ExtensionConfiguration();
        $builder = new BeGroupFieldCollectionBuilder($config);

        $configurationArray = [
            'title' => 'Group title',
            'non_exclude_fields' => [
                'pages' => [
                    'title',
                    'media'
                ]
            ],
            'tables_select' => ['pages','tt_content','tx_news_domain_model_link','tx_news_domain_model_news']
        ];

        $collection = $builder->buildFromConfigurationArray($configurationArray);

        $expectedCollection = new BeGroupFieldCollection();
        $expectedCollection->add(
            NonExcludeFields::createFromConfigurationArray(
                [
                    'pages' => [
                        'title',
                        'media'
                    ]
                ]
            )
        );
        $expectedCollection->add(
            TablesSelect::createFromConfigurationArray(
                ['pages','tt_content','tx_news_domain_model_link','tx_news_domain_model_news']
            )
        );

        $this->assertEquals($expectedCollection, $collection);
    }
}
