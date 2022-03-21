<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\BeGroupFieldInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\NonExcludeFields;

/**
 * @covers \Pluswerk\BePermissions\Value\NonExcludeFields
 */
final class NonExcludeFieldsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function no_error_with_empty_database_field(): void //phpcs:ignore
    {
        $nonExcludeFields = NonExcludeFields::createFromDBValue('');
    }

    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_array(): void //phpcs:ignore
    {
        $dbValue = 'tt_content:pages,tt_content:date,pages:media,pages:hidden';

        $nonExcludeFields = NonExcludeFields::createFromDBValue($dbValue);

        $this->assertSame(
            [
                'pages' => ['hidden', 'media'],
                'tt_content' => ['date', 'pages']
            ],
            $nonExcludeFields->yamlConfigurationValue()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration(
            [
                'tt_content' => ['pages', 'date'],
                'pages' => ['media', 'hidden']
            ]
        );

        $this->assertSame(
            'pages:hidden,pages:media,tt_content:date,tt_content:pages',
            (string)$nonExcludeFields
        );
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_non_exclude_fields_object(): void //phpcs:ignore
    {
        $baseNonExcludeFields = NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => ['media', 'hidden'],
                'tt_content' => ['pages', 'date']
            ]
        );

        $extendingNonExcludeFields = NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => ['media', 'title'],
                'tt_content' => ['pages', 'date', 'another_field']
            ]
        );

        $expectedNonExcludeFields = NonExcludeFields::createFromYamlConfiguration(
            [
                'pages' => ['hidden', 'media', 'title'],
                'tt_content' => ['another_field', 'date', 'pages']
            ]
        );

        $this->assertEquals($expectedNonExcludeFields, $baseNonExcludeFields->extend($extendingNonExcludeFields));
    }

    /**
     * @test
     */
    public function field_name_is_non_exclude_fields(): void //phpcs:ignore
    {
        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration([]);
        $this->assertSame('non_exclude_fields', $nonExcludeFields->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void //phpcs:ignore
    {
        $nonExcludeFields = NonExcludeFields::createFromYamlConfiguration([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $nonExcludeFields);
    }
}
