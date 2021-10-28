<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\BeGroupFieldInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\TablesModify;

/**
 * @covers \Pluswerk\BePermissions\Value\TablesModify
 */
final class TablesModifyTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void //phpcs:ignore
    {
        $dbValue = 'pages,sys_category,sys_file,sys_file_metadata,sys_file_reference,tt_content,tx_news_domain_model_link,tx_news_domain_model_news,tx_basepackage_accordion_content';

        $tablesSelect = TablesModify::createFromDBValue($dbValue);

        $this->assertSame(
            ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content','tx_news_domain_model_link','tx_news_domain_model_news','tx_basepackage_accordion_content'],
            $tablesSelect->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $confArray = ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content','tx_news_domain_model_link','tx_news_domain_model_news','tx_basepackage_accordion_content'];

        $tablesSelect = TablesModify::createFromYamlConfiguration($confArray);

        $this->assertSame(
            'pages,sys_category,sys_file,sys_file_metadata,sys_file_reference,tt_content,tx_news_domain_model_link,tx_news_domain_model_news,tx_basepackage_accordion_content',
            (string)$tablesSelect
        );
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_db_mountpoints_array_is_returned(): void //phpcs:ignore
    {
        $tablesSelect = TablesModify::createFromDBValue('');

        $this->assertSame([], $tablesSelect->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_db_mountpoints_object(): void //phpcs:ignore
    {
        $confArray = ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content'];

        $tablesSelect = TablesModify::createFromYamlConfiguration($confArray);
        $extendTablesSelect = TablesModify::createFromYamlConfiguration(['tt_content','tx_news_domain_model_link','tx_news_domain_model_news','tx_basepackage_accordion_content']);

        $this->assertSame(
            ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content','tx_basepackage_accordion_content','tx_news_domain_model_link','tx_news_domain_model_news'],
            $tablesSelect->extend($extendTablesSelect)->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function field_name_is_allowed_languages(): void //phpcs:ignore
    {
        $tablesSelect = TablesModify::createFromYamlConfiguration([]);
        $this->assertSame('tables_modify', $tablesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void //phpcs:ignore
    {
        $tablesSelect = TablesModify::createFromYamlConfiguration([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $tablesSelect);
    }
}
