<?php

declare(strict_types=1);

namespace Pluswerk\BePermissions\Tests\Unit\Value;

use Pluswerk\BePermissions\Value\BeGroupFieldInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pluswerk\BePermissions\Value\AllowedLanguages;

/**
 * @covers \Pluswerk\BePermissions\Value\AllowedLanguages
 */
final class AllowedLanguagesTest extends UnitTestCase
{
    /**
     * @test
     */
    public function can_be_created_from_database_value_and_returned_as_configuration_array(): void //phpcs:ignore
    {
        $dbValue = '0,3,5';

        $allowedLanguages = AllowedLanguages::createFromDBValue($dbValue);

        $this->assertSame([0,3,5], $allowedLanguages->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_created_from_configuration_array_and_returned_as_database_value(): void //phpcs:ignore
    {
        $confArray = [0,3,5];

        $allowedLanguages = AllowedLanguages::createFromYamlConfiguration($confArray);

        $this->assertSame('0,3,5', (string)$allowedLanguages);
    }

    /**
     * @test
     */
    public function with_empty_database_field_an_empty_language_array_is_returned(): void //phpcs:ignore
    {
        $allowedLanguages = AllowedLanguages::createFromDBValue('');

        $this->assertSame([], $allowedLanguages->yamlConfigurationValue());
    }

    /**
     * @test
     */
    public function can_be_extended_by_another_allowed_languages_object(): void //phpcs:ignore
    {
        $confArray = [0,3,5];

        $allowedLanguages = AllowedLanguages::createFromYamlConfiguration($confArray);

        $extendAllowedLanguages = AllowedLanguages::createFromYamlConfiguration([3,4,5]);

        $this->assertSame('0,3,4,5', (string)($allowedLanguages->extend($extendAllowedLanguages)));
    }

    /**
     * @test
     */
    public function field_name_is_allowed_languages(): void //phpcs:ignore
    {
        $allowedLanguages = AllowedLanguages::createFromYamlConfiguration([]);
        $this->assertSame('allowed_languages', $allowedLanguages->getFieldName());
    }

    /**
     * @test
     */
    public function implements_be_group_field_interface(): void //phpcs:ignore
    {
        $allowedLanguages = AllowedLanguages::createFromYamlConfiguration([]);
        $this->assertInstanceOf(BeGroupFieldInterface::class, $allowedLanguages);
    }
}
