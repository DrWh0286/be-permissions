<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\AbstractBooleanField;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\CodeManagedGroup;

/**
 * @covers \Pluswerk\BePermissions\Value\CodeManagedGroup
 */
final class CodeManagedGroupTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_value(): void // phpcs:ignore
    {
        $dbValue = '1';

        $bulkExport = CodeManagedGroup::createFromDBValue($dbValue);

        $this->assertTrue($bulkExport->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_value_and_returned_as_database_value(): void // phpcs:ignore
    {
        $yamlValue = true;

        $bulkExport = CodeManagedGroup::createFromYamlConfiguration($yamlValue);

        $this->assertEquals('1', (string)$bulkExport);
    }

    /**
     * @test
     */
    public function with_empty_database_field_the_value_is_false(): void // phpcs:ignore
    {
        $bulkExport = CodeManagedGroup::createFromDBValue('');

        $this->assertFalse($bulkExport->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function field_name_is_bulk_export(): void // phpcs:ignore
    {
        $bulkExport = CodeManagedGroup::createFromDBValue('');

        $this->assertSame('code_managed_group', $bulkExport->getFieldName());
    }

    /**
     * @test
     */
    public function implements_AbstractBooleanField(): void // phpcs:ignore
    {
        $bulkExport = CodeManagedGroup::createFromDBValue('');

        $this->assertInstanceOf(AbstractBooleanField::class, $bulkExport);
    }

    /**
     * @test
     */
    public function is_overruled_by_extend(): void // phpcs:ignore
    {
        $yamlValue = true;

        $bulkExport = CodeManagedGroup::createFromYamlConfiguration($yamlValue);
        $extendBulkExport = CodeManagedGroup::createFromYamlConfiguration(false);

        $resultBulkExport = $bulkExport->extend($extendBulkExport);

        $this->assertFalse($resultBulkExport->yamlConfigurationValue());
        $this->assertNotSame($bulkExport, $resultBulkExport);
        $this->assertNotSame($extendBulkExport, $resultBulkExport);
    }
}
