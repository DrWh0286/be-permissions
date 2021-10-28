<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\BeGroupFieldInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\TablesSelect;

/**
 * @covers \Pluswerk\BePermissions\Value\TablesSelect
 */
final class TablesSelectTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void
    {
        $dbValue = 'pages,sys_category,sys_file,sys_file_metadata,sys_file_reference,tt_content,tx_news_domain_model_link,tx_news_domain_model_news,tx_basepackage_accordion_content';

        $tablesSelect = TablesSelect::createFromDBValue($dbValue);

        $this->assertSame(
            ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content','tx_news_domain_model_link','tx_news_domain_model_news','tx_basepackage_accordion_content'],
            $tablesSelect->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void
    {
        $confArray = ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content','tx_news_domain_model_link','tx_news_domain_model_news','tx_basepackage_accordion_content'];

        $tablesSelect = TablesSelect::createFromYamlConfiguration($confArray);

        $this->assertSame(
            'pages,sys_category,sys_file,sys_file_metadata,sys_file_reference,tt_content,tx_news_domain_model_link,tx_news_domain_model_news,tx_basepackage_accordion_content',
            (string)$tablesSelect
        );
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_db_mountpoints_array_is_returned(): void
    {
        $tablesSelect = TablesSelect::createFromDBValue('');

        $this->assertSame([], $tablesSelect->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_db_mountpoints_object(): void
    {
        $confArray = ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content'];

        $tablesSelect = TablesSelect::createFromYamlConfiguration($confArray);
        $extendTablesSelect = TablesSelect::createFromYamlConfiguration(['tt_content','tx_news_domain_model_link','tx_news_domain_model_news','tx_basepackage_accordion_content']);

        $this->assertSame(
            ['pages','sys_category','sys_file','sys_file_metadata','sys_file_reference','tt_content','tx_basepackage_accordion_content','tx_news_domain_model_link','tx_news_domain_model_news'],
            $tablesSelect->extend($extendTablesSelect)->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function field_name_is_allowed_languages(): void
    {
        $tablesSelect = TablesSelect::createFromYamlConfiguration([]);
        $this->assertSame('tables_select', $tablesSelect->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void
    {
        $tablesSelect = TablesSelect::createFromYamlConfiguration([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $tablesSelect);
    }
}
