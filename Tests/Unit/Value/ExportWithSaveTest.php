<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\AbstractBooleanField;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\ExportWithSave;

/**
 * @covers \Pluswerk\BePermissions\Value\ExportWithSave
 */
final class ExportWithSaveTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_value(): void // phpcs:ignore
    {
        $dbValue = '1';

        $exportWithSave = ExportWithSave::createFromDBValue($dbValue);

        $this->assertTrue($exportWithSave->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_value_and_returned_as_database_value(): void // phpcs:ignore
    {
        $yamlValue = true;

        $exportWithSave = ExportWithSave::createFromYamlConfiguration($yamlValue);

        $this->assertEquals('1', (string)$exportWithSave);
    }

    /**
     * @test
     */
    public function with_empty_database_field_the_value_is_false(): void // phpcs:ignore
    {
        $exportWithSave = ExportWithSave::createFromDBValue('');

        $this->assertFalse($exportWithSave->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function field_name_is_bulk_export(): void // phpcs:ignore
    {
        $exportWithSave = ExportWithSave::createFromDBValue('');

        $this->assertSame('export_with_save', $exportWithSave->getFieldName());
    }

    /**
     * @test
     */
    public function implements_AbstractBooleanField(): void // phpcs:ignore
    {
        $exportWithSave = ExportWithSave::createFromDBValue('');

        $this->assertInstanceOf(AbstractBooleanField::class, $exportWithSave);
    }

    /**
     * @test
     */
    public function is_overruled_by_extend(): void // phpcs:ignore
    {
        $yamlValue = true;

        $exportWithSave = ExportWithSave::createFromYamlConfiguration($yamlValue);
        $extendExportWithSave = ExportWithSave::createFromYamlConfiguration(false);

        $resultExportWithSave = $exportWithSave->extend($extendExportWithSave);

        $this->assertFalse($resultExportWithSave->yamlConfigurationValue());
        $this->assertNotSame($exportWithSave, $resultExportWithSave);
        $this->assertNotSame($extendExportWithSave, $resultExportWithSave);
    }
}
