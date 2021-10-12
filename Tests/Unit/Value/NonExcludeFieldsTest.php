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
    public function no_error_with_empty_database_field(): void
    {
        $nonExcludeFields = NonExcludeFields::createFromDBValue('');
    }

    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_array(): void
    {
        $dbValue = 'pages:media,pages:hidden,tt_content:pages,tt_content:date';

        $nonExcludeFields = NonExcludeFields::createFromDBValue($dbValue);

        $this->assertSame(
            [
                'pages' => ['media', 'hidden'],
                'tt_content' => ['pages', 'date']
            ],
            $nonExcludeFields->asArray()
        );
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void
    {
        $nonExcludeFields = NonExcludeFields::createFromConfigurationArray(
            [
                'pages' => ['media', 'hidden'],
                'tt_content' => ['pages', 'date']
            ]
        );

        $this->assertSame(
            'pages:media,pages:hidden,tt_content:pages,tt_content:date',
            (string)$nonExcludeFields
        );
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_non_exclude_fields_object(): void
    {
        $baseNonExcludeFields = NonExcludeFields::createFromConfigurationArray(
            [
                'pages' => ['media', 'hidden'],
                'tt_content' => ['pages', 'date']
            ]
        );

        $extendingNonExcludeFields = NonExcludeFields::createFromConfigurationArray(
            [
                'pages' => ['media', 'title'],
                'tt_content' => ['pages', 'date', 'another_field']
            ]
        );

        $expectedNonExcludeFields = NonExcludeFields::createFromConfigurationArray(
            [
                'pages' => ['media', 'hidden', 'title'],
                'tt_content' => ['pages', 'date', 'another_field']
            ]
        );

        $this->assertEquals($expectedNonExcludeFields, $baseNonExcludeFields->extend($extendingNonExcludeFields));
    }

    /**
     * @test
     */
    public function field_name_is_non_exclude_fields(): void
    {
        $nonExcludeFields = NonExcludeFields::createFromConfigurationArray([]);
        $this->assertSame('non_exclude_fields', $nonExcludeFields->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void
    {
        $nonExcludeFields = NonExcludeFields::createFromConfigurationArray([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $nonExcludeFields);
    }
}
