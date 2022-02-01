<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\AbstractBooleanField;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\BulkExport;

/**
 * @covers \Pluswerk\BePermissions\Value\BulkExport
 */
final class BulkExportTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_value(): void // phpcs:ignore
    {
        $dbValue = '1';

        $bulkExport = BulkExport::createFromDBValue($dbValue);

        $this->assertTrue($bulkExport->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_value_and_returned_as_database_value(): void // phpcs:ignore
    {
        $yamlValue = true;

        $bulkExport = BulkExport::createFromYamlConfiguration($yamlValue);

        $this->assertEquals('1', (string)$bulkExport);
    }

    /**
     * @test
     */
    public function with_empty_database_field_the_value_is_false(): void // phpcs:ignore
    {
        $bulkExport = BulkExport::createFromDBValue('');

        $this->assertFalse($bulkExport->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function field_name_is_bulk_export(): void // phpcs:ignore
    {
        $bulkExport = BulkExport::createFromDBValue('');

        $this->assertSame('bulk_export', $bulkExport->getFieldName());
    }

    /**
     * @test
     */
    public function implements_AbstractBooleanField(): void // phpcs:ignore
    {
        $bulkExport = BulkExport::createFromDBValue('');

        $this->assertInstanceOf(AbstractBooleanField::class, $bulkExport);
    }

    /**
     * @test
     */
    public function is_overruled_by_extend(): void // phpcs:ignore
    {
        $yamlValue = true;

        $bulkExport = BulkExport::createFromYamlConfiguration($yamlValue);
        $extendBulkExport = BulkExport::createFromYamlConfiguration(false);

        $resultBulkExport = $bulkExport->extend($extendBulkExport);

        $this->assertFalse($resultBulkExport->yamlConfigurationValue());
        $this->assertNotSame($bulkExport, $resultBulkExport);
        $this->assertNotSame($extendBulkExport, $resultBulkExport);
    }
}
